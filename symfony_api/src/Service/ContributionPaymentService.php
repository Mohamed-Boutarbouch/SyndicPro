<?php

namespace App\Service;

use App\DTO\Request\RecordPaymentRequest;
use App\Entity\LedgerEntry;
use App\Entity\Receipt;
use App\Entity\ReceiptTemplate;
use App\Enum\LedgerEntryIncomeType;
use App\Enum\LedgerEntryType;
use App\Repository\BuildingRepository;
use App\Repository\UnitRepository;
use App\Repository\UserRepository;
use App\Repository\ContributionScheduleRepository;
use App\Repository\RegularContributionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class ContributionPaymentService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UnitRepository $unitRepository,
        private BuildingRepository $buildingRepository,
        private UserRepository $userRepository,
        private RegularContributionRepository $regularContributionRepository,
        private ContributionScheduleRepository $contributionScheduleRepository,
        private ReceiptGenerator $receiptGenerator,
        private LoggerInterface $logger
    ) {
    }

    public function recordPayment(int $buildingId, RecordPaymentRequest $request): LedgerEntry
    {
        try {
            $this->logger->info('Starting payment recording process', [
                'buildingId' => $buildingId,
                'unitId' => $request->unitId,
                'amount' => $request->amount
            ]);

            $unit = $this->unitRepository->find($request->unitId);

            $user = $this->userRepository->find(1);

            if (!$unit) {
                $this->logger->warning('Unit not found', [
                    'unitId' => $request->unitId,
                    'buildingId' => $buildingId
                ]);
                throw new \InvalidArgumentException('Unit not found or does not belong to the specified building');
            }

            $this->logger->info('Unit found', [
                'unitId' => $unit->getId(),
                'unitNumber' => $unit->getNumber()
            ]);

            $building = $this->buildingRepository->find($buildingId);

            $schedule = $this->contributionScheduleRepository
                ->findByRegularContributionAndUnit(
                    $request->regularContributionId,
                    $unit->getId()
                );

            $ledgerEntry = new LedgerEntry();
            $ledgerEntry->setBuilding($building);
            $ledgerEntry->setType(LedgerEntryType::INCOME);
            $ledgerEntry->setAmount($request->amount);
            $ledgerEntry->setDescription('Contribution payment for unit ' . $unit->getNumber());
            $ledgerEntry->setIncomeType(LedgerEntryIncomeType::REGULAR_CONTRIBUTION);
            $ledgerEntry->setUnit($unit);
            $ledgerEntry->setReferenceNumber($request->reference);
            $ledgerEntry->setPaymentMethod($request->paymentMethod);
            $ledgerEntry->setContributionSchedule($schedule);
            $ledgerEntry->setRecordedBy($user);

            if (!$schedule) {
                throw new \RuntimeException('Contribution schedule not found for this unit.');
            }

            $frequency = $schedule->getFrequency()?->value;
            $nextDueDate = $schedule->getNextDueDate();

            switch ($frequency) {
                case 'monthly':
                    $nextDueDate = (clone $nextDueDate)->modify('last day of next month');
                    break;
                case 'bi_monthly':
                    $nextDueDate = (clone $nextDueDate)->modify('last day of +2 months');
                    break;
                case 'quarterly':
                    $nextDueDate = (clone $nextDueDate)->modify('last day of +3 months');
                    break;
                case 'four_monthly':
                    $nextDueDate = (clone $nextDueDate)->modify('last day of +4 months');
                    break;
                case 'yearly':
                    $nextDueDate = (clone $nextDueDate)->modify('last day of December +1 year');
                    break;
                default:
                    throw new \RuntimeException('Unknown contribution frequency: ' . $frequency);
            }

            $schedule->setNextDueDate($nextDueDate);

            if (
                $ledgerEntry->getType() === LedgerEntryType::INCOME &&
                $ledgerEntry->getIncomeType() === LedgerEntryIncomeType::REGULAR_CONTRIBUTION
            ) {
                try {
                    $template = $this->entityManager->getRepository(ReceiptTemplate::class)
                        ->findOneBy(['building' => $building, 'createdBy' => $user]);

                    if ($template) {
                        $receiptData = [
                            'fullName'    => $unit->getUser()->getFirstName() . ' ' . strtoupper($unit->getUser()->getLastName()),
                            'unitNumber'  => $unit->getNumber(),
                            'createdDate' => (new \DateTimeImmutable())->format('d/m/Y'),
                            'amount'      => number_format($request->amount, 2) . ' MAD',
                        ];

                        $pdfBytes = $this->receiptGenerator->generateToStream($template, $receiptData);

                        // Create and persist a new Receipt entity linked to the payment
                        $receipt = new Receipt();
                        $receipt->setLedgerEntry($ledgerEntry);
                        $receipt->setGeneratedBy($user);
                        $receipt->setNumber(Uuid::v4()->toRfc4122());
                        $receipt->setBuilding($building);
                        $receipt->setUnit($unit);
                        $receipt->setContributionSchedule($schedule);
                        $receipt->setTemplate($template);


                        // Ensure upload folder exists
                        $uploadDir = __DIR__ . '/../../public/uploads/receipts';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        // Define file name
                        $fileName = 'receipt_' . $receipt->getNumber() . '.pdf';
                        $filePath = $uploadDir . '/' . $fileName;

                        // Save PDF to disk
                        file_put_contents($filePath, $pdfBytes);

                        // Store relative path in DB
                        $receipt->setFilePath('uploads/receipts/' . $fileName);

                        $this->entityManager->persist($receipt);
                        $this->entityManager->flush();

                        $ledgerEntry->setReceipt($receipt);
                    }
                } catch (\Exception $e) {
                    $this->logger->error('Receipt generation failed', [
                        'ledgerEntryId' => $ledgerEntry->getId(),
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->entityManager->persist($ledgerEntry);
            $this->entityManager->flush();

            $this->logger->info('Payment recorded successfully', [
                'ledgerEntryId' => $ledgerEntry->getId()
            ]);

            return $ledgerEntry;
        } catch (\InvalidArgumentException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('Failed to record payment', [
                'error' => $e->getMessage(),
                'buildingId' => $buildingId,
                'unitId' => $request->unitId
            ]);
            throw new \RuntimeException('Failed to record payment: ' . $e->getMessage());
        }
    }
}
