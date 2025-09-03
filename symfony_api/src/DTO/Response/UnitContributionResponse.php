<?php

namespace App\DTO\Response;

use Symfony\Component\Serializer\Annotation\Groups;

class UnitContributionResponse
{
    // Existing "overview" fields
    #[Groups(['contribution:overview'])]
    public int $unitId;

    #[Groups(['contribution:overview'])]
    public string $unitNumber;

    #[Groups(['contribution:overview'])]
    public string $ownerFirstName;

    #[Groups(['contribution:overview'])]
    public string $ownerLastName;

    #[Groups(['contribution:overview'])]
    public string $ownerFullName;

    #[Groups(['contribution:overview'])]
    public string $frequency;

    #[Groups(['contribution:overview'])]
    public float $amountPerPayment;

    #[Groups(['contribution:overview'])]
    public ?string $nextDueDate;

    #[Groups(['contribution:overview'])]
    public float $totalPaid;

    #[Groups(['contribution:overview'])]
    public string $paymentStatus;

    #[Groups(['contribution:stats'])]
    public int $buildingId;

    #[Groups(['contribution:stats'])]
    public string $buildingName;

    #[Groups(['contribution:stats'])]
    public int $paymentYear;

    #[Groups(['contribution:stats'])]
    public string $periodAnnualAmount;

    #[Groups(['contribution:stats'])]
    public string $periodStartDate;

    #[Groups(['contribution:stats'])]
    public int $regularContributionId;

    #[Groups(['contribution:stats'])]
    public float $totalAnnualAmount;

    #[Groups(['contribution:stats'])]
    public float $totalPaidAmount;

    #[Groups(['contribution:stats'])]
    public int $totalPayments;

    /**
     * Creates a UnitContributionResponse from an array of contribution data.
     */
    public static function fromData(array $data): self
    {
        $dto = new self();

        $dto->unitId = (int) ($data['unitId'] ?? 0);
        $dto->unitNumber = (string) ($data['unitNumber'] ?? '');

        $dto->ownerFullName = trim(($data['ownerLastName'] ?? '') . ' ' . ($data['ownerFirstName'] ?? ''));

        $dto->frequency = (string) ($data['frequency'] ?? '');
        $dto->amountPerPayment = (float) ($data['amountPerPayment'] ?? 0.0);
        $dto->nextDueDate = $data['nextDueDate'] ?? null;
        $dto->totalPaid = (float) ($data['totalPaid'] ?? 0.0);

        if ($dto->nextDueDate !== null) {
            $now = new \DateTimeImmutable();
            $nextDue = new \DateTimeImmutable($dto->nextDueDate);
            $dto->paymentStatus = $now > $nextDue ? 'overdue' : 'paid';
        } else {
            $dto->paymentStatus = 'paid';
        }

        $dto->buildingId = (int) ($data['buildingId'] ?? 0);
        $dto->buildingName = (string) ($data['buildingName'] ?? '');
        $dto->paymentYear = (int) ($data['paymentYear'] ?? 0);
        $dto->periodAnnualAmount = (string) ($data['periodAnnualAmount'] ?? '');
        $dto->periodStartDate = (string) ($data['periodStartDate'] ?? '');
        $dto->regularContributionId = (int) ($data['regularContributionId'] ?? 0);
        $dto->totalAnnualAmount = (float) ($data['totalAnnualAmount'] ?? 0.0);
        $dto->totalPaidAmount = (float) ($data['totalPaidAmount'] ?? 0.0);
        $dto->totalPayments = (int) ($data['totalPayments'] ?? 0);

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
