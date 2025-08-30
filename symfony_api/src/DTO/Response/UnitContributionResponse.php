<?php

namespace App\DTO\Response;

use Symfony\Component\Serializer\Annotation\Groups;

class UnitContributionResponse
{
    #[Groups(['contribution:overview'])]
    public int $unitId;

    #[Groups(['contribution:overview'])]
    public string $unitNumber;

    #[Groups(['contribution:overview'])]
    public string $ownerName;

    #[Groups(['contribution:overview'])]
    public string $frequency;

    #[Groups(['contribution:overview'])]
    public float $amountPerPayment;

    #[Groups(['contribution:overview'])]
    public ?string $nextDueDate;

    #[Groups(['contribution:overview'])]
    public ?string $lastPayment;

    #[Groups(['contribution:overview'])]
    public float $totalPaid;

    /**
     * Creates a UnitContributionResponse from an array of unit contribution data.
     */
    public static function fromData(array $data): self
    {
        $dto = new self();
        $dto->unitId = (int) ($data['unitId'] ?? 0);
        $dto->unitNumber = (string) ($data['unitNumber'] ?? '');
        $dto->ownerName = (string) ($data['ownerName'] ?? '');
        $dto->frequency = (string) ($data['frequency'] ?? '');
        $dto->amountPerPayment = (float) ($data['amountPerPayment'] ?? 0.0);
        $dto->nextDueDate = $data['nextDueDate'] ?? null;
        $dto->lastPayment = $data['lastPayment'] ?? null;
        $dto->totalPaid = (float) ($data['totalPaid'] ?? 0.0);

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
