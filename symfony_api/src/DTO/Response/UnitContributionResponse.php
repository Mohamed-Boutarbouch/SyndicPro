<?php

namespace App\DTO\Response;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Enum\ContributionFrequency;

class UnitContributionResponse
{
    #[Groups(['contribution:schedule-table'])]
    public int $ownerId;

    #[Groups(['contribution:schedule-table'])]
    public int $unitId;

    #[Groups(['contribution:schedule-table'])]
    public string $unitNumber;

    #[Groups(['contribution:schedule-table'])]
    public string $ownerFirstName;

    #[Groups(['contribution:schedule-table'])]
    public string $ownerLastName;

    #[Groups(['contribution:schedule-table'])]
    public string $ownerFullName;

    #[Groups(['contribution:schedule-table'])]
    public string $frequency;

    #[Groups(['contribution:schedule-table'])]
    public float $amountPerPayment;

    #[Groups(['contribution:schedule-table'])]
    public ?\DateTimeImmutable $nextDueDate;

    #[Groups(['contribution:schedule-table'])]
    public string $paymentStatus;

    #[Groups(['contribution:schedule-table'])]
    public float $scheduleId;

    #[Groups(['contribution:schedule-table'])]
    public float $unitFloor;

    #[Groups(['contribution:card-stats', 'contribution:schedule-table'])]
    public int $buildingId;

    #[Groups(['contribution:card-stats'])]
    public string $buildingName;

    #[Groups(['contribution:card-stats'])]
    public int $paymentYear;

    #[Groups(['contribution:card-stats'])]
    public ?\DateTimeImmutable $periodStartDate;

    #[Groups(['contribution:card-stats'])]
    public ?\DateTimeImmutable $periodEndDate;

    #[Groups(['contribution:card-stats'])]
    public float $amountPerUnit;

    #[Groups(['contribution:card-stats', 'contribution:schedule-table'])]
    public int $regularContributionId;

    #[Groups(['contribution:card-stats'])]
    public float $totalAnnualAmount;

    #[Groups(['contribution:card-stats'])]
    public float $totalPaidAmount;

    #[Groups(['contribution:schedule-table'])]
    public float $actualPaidAmountPerUnit;

    #[Groups(['contribution:card-stats'])]
    public int $totalPaymentCount;

    /**
     * Creates a UnitContributionResponse from an array of contribution data.
     */
    public static function fromData(array $data): self
    {
        $dto = new self();

        $dto->ownerId = (int) ($data['ownerId'] ?? 0);
        $dto->unitId = (int) ($data['unitId'] ?? 0);
        $dto->unitNumber = (string) ($data['unitNumber'] ?? '');

        $dto->ownerFullName = trim(($data['ownerLastName'] ?? '') . ' ' . ($data['ownerFirstName'] ?? ''));

        $dto->frequency = isset($data['frequency'])
            ? ($data['frequency'] instanceof ContributionFrequency ? $data['frequency']->value : (string) $data['frequency'])
            : '';

        $dto->amountPerPayment = (float) ($data['amountPerPayment'] ?? 0.0);

        // nextDueDate
        $dto->nextDueDate = match (true) {
            empty($data['nextDueDate']) => null,
            $data['nextDueDate'] instanceof \DateTimeInterface => \DateTimeImmutable::createFromInterface($data['nextDueDate']),
            default => new \DateTimeImmutable((string) $data['nextDueDate']),
        };

        // periodStartDate
        $dto->periodStartDate = match (true) {
            empty($data['periodStartDate']) => null,
            $data['periodStartDate'] instanceof \DateTimeInterface => \DateTimeImmutable::createFromInterface($data['periodStartDate']),
            default => new \DateTimeImmutable((string) $data['periodStartDate']),
        };

        // periodEndDate
        $dto->periodEndDate = match (true) {
            empty($data['periodEndDate']) => null,
            $data['periodEndDate'] instanceof \DateTimeInterface => \DateTimeImmutable::createFromInterface($data['periodEndDate']),
            default => new \DateTimeImmutable((string) $data['periodEndDate']),
        };

        $dto->amountPerUnit = (float) ($data['amountPerUnit'] ?? 0.0);
        $dto->amountPerUnit = (float) ($data['amountPerUnit'] ?? 0.0);
        $dto->actualPaidAmountPerUnit = (float) ($data['actualPaidAmountPerUnit'] ?? 0.0); // move this up

        $now = new \DateTimeImmutable();
        if ($dto->actualPaidAmountPerUnit >= $dto->amountPerPayment) {
            $dto->paymentStatus = 'paid';
        } elseif ($dto->nextDueDate && $now > $dto->nextDueDate) {
            $dto->paymentStatus = 'overdue';
        } else {
            $dto->paymentStatus = 'pending';
        }

        $dto->buildingId = (int) ($data['buildingId'] ?? 0);
        $dto->buildingName = (string) ($data['buildingName'] ?? '');
        $dto->paymentYear = (int) ($data['paymentYear'] ?? 0);
        $dto->regularContributionId = (int) ($data['regularContributionId'] ?? 0);
        $dto->totalAnnualAmount = (float) ($data['totalAnnualAmount'] ?? 0.0);
        $dto->totalPaidAmount = (float) ($data['totalPaidAmount'] ?? 0.0);
        $dto->totalPaymentCount = (int) ($data['totalPaymentCount'] ?? 0);

        $dto->scheduleId = (int) ($data['scheduleId'] ?? 0);
        $dto->unitFloor = (int) ($data['unitFloor'] ?? 0);

        return $dto;
    }

    /**
     * Creates an array of UnitContributionResponse from an array of unit contribution data.
     *
     * @param array $dataArray Array of unit contribution data arrays
     * @return self[]
     */
    public static function fromDataArray(array $dataArray): array
    {
        return array_map([self::class, 'fromData'], $dataArray);
    }
}
