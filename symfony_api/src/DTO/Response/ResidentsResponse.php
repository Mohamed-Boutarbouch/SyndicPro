<?php

namespace App\DTO\Response;

use Symfony\Component\Serializer\Annotation\Groups;
use App\Enum\UnitType;

class ResidentsResponse
{
    #[Groups(['residents', 'form'])]
    public int $unitId;

    #[Groups(['residents'])]
    public int $userId;

    #[Groups(['residents', 'form'])]
    public string $firstName;

    #[Groups(['residents', 'form'])]
    public string $lastName;

    #[Groups(['residents'])]
    public string $email;

    #[Groups(['residents', 'form'])]
    public string $number;

    #[Groups(['residents', 'form'])]
    public float $expectedPayment;

    #[Groups(['residents'])]
    public string $unitType;

    /**
     * Creates a ResidentsResponse from an array of resident data.
     */
    public static function fromData(array $data): self
    {
        $dto = new self();

        $dto->unitId          = (int)     ($data['unitId'] ?? 0);
        $dto->userId          = (int)     ($data['userId'] ?? 0);
        $dto->firstName       = (string)  ($data['firstName'] ?? '');
        $dto->lastName        = (string)  ($data['lastName'] ?? '');
        $dto->email           = (string)  ($data['email'] ?? '');
        $dto->number          = (string)  ($data['number'] ?? '');
        $dto->expectedPayment = (float)   ($data['expectedPayment'] ?? 0.00);

        $dto->unitType  = $data['unitType'] instanceof UnitType
            ? $data['unitType']->value
            : (string)($data['unitType'] ?? '');

        return $dto;
    }

    /**
     * Creates an array of ResidentsResponse from an array of resident data arrays.
     *
     * @param array $dataArray Array of resident data arrays
     * @return self[]
     */
    public static function fromDataArray(array $dataArray): array
    {
        return array_map([self::class, 'fromData'], $dataArray);
    }
}
