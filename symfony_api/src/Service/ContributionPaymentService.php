<?php

namespace App\Service;

use App\DTO\Request\RecordPaymentRequest;
use App\Entity\LedgerEntry;
use App\Enum\LedgerEntryIncomeType;
use App\Enum\LedgerEntryType;
use App\Repository\BuildingRepository;
use App\Repository\UnitRepository;
use App\Repository\UserRepository;
use App\Repository\ContributionScheduleRepository;
use App\Repository\RegularContributionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ContributionPaymentService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UnitRepository $unitRepository,
        private BuildingRepository $buildingRepository,
        private UserRepository $userRepository,
        private RegularContributionRepository $regularContributionRepository,
        private ContributionScheduleRepository $contributionScheduleRepository,
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
