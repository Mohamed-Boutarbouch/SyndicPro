<?php

namespace App\Service;

use App\DTO\Request\RecordPaymentRequest;
use App\Entity\Payment;
use App\Entity\Transaction;
use App\Enum\TransactionStatus;
use App\Enum\TransactionType;
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

    // $this->entityManager->refresh($user);
    public function recordPayment(int $buildingId, RecordPaymentRequest $request): Payment
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


            $regularContributionId = 1; // hardcoded for now
            $schedule = $this->contributionScheduleRepository
                ->findByRegularContributionAndUnit($regularContributionId, $unit->getId());

            $payment = new Payment();
            $payment->setContributionSchedule($schedule);
            $payment->setAmount($request->amount);
            $payment->setDate($request->paymentDate);
            $payment->setMethod($request->paymentMethod);
            $payment->setReferenceNumber($request->reference);
            $payment->setNotes($request->notes);
            $payment->setRecorderBy($user);

            $building = $this->buildingRepository->find($buildingId);

            $transaction = new Transaction();
            $transaction->setBuilding($building);
            $transaction->setType(TransactionType::INCOME);
            $transaction->setAmount($request->amount);
            $transaction->setDate(new \DateTimeImmutable());
            $transaction->setDescription('Contribution payment for unit ' . $unit->getNumber());
            $transaction->setUnit($unit);
            $transaction->setReferenceNumber($request->reference);
            $transaction->setPaymentMethod($request->paymentMethod);
            $transaction->setStatus(TransactionStatus::PAID);
            $transaction->setApprovedBy($user);
            $transaction->setApprovedAt(new \DateTimeImmutable());

            if (!$schedule) {
                throw new \RuntimeException('Contribution schedule not found for this unit.');
            }

            // Calculate next due date
            $frequency = $schedule->getFrequency()?->value;
            $nextDueDate = $schedule->getNextDueDate();

            switch ($frequency) {
                case 'monthly':
                    $nextDueDate = $nextDueDate->modify('+1 month');
                    break;
                case 'bi_monthly':
                    $nextDueDate = $nextDueDate->modify('+2 months');
                    break;
                case 'quarterly':
                    $nextDueDate = $nextDueDate->modify('+3 months');
                    break;
                case 'four_monthly':
                    $nextDueDate = $nextDueDate->modify('+4 months');
                    break;
                case 'yearly':
                    $nextDueDate = $nextDueDate->modify('+1 year');
                    break;
                default:
                    throw new \RuntimeException('Unknown contribution frequency: ' . $frequency);
            }

            // Update the schedule
            $schedule->setNextDueDate($nextDueDate);
            $this->entityManager->persist($schedule);

            $this->entityManager->persist($payment);
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();

            $this->logger->info('Payment recorded successfully', [
                'paymentId' => $payment->getId()
            ]);

            return $payment;
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
