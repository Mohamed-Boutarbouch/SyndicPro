<?php

namespace App\DTO\Response;

use Symfony\Component\Serializer\Annotation\Groups;

class DashboardResponse
{
    #[Groups(['dashboard:default'])]
    public float $currentBalance = 0.0;

    #[Groups(['dashboard:default'])]
    public float $lastMonthBalance = 0.0;

    #[Groups(['dashboard:default'])]
    public ?float $balancePercentChange = null;

    #[Groups(['dashboard:default'])]
    public float $currentMonthIncome = 0.0;

    #[Groups(['dashboard:default'])]
    public float $previousMonthIncome = 0.0;

    #[Groups(['dashboard:default'])]
    public ?float $incomePercentChange = null;

    #[Groups(['dashboard:default'])]
    public int $totalPendingItems = 0;

    #[Groups(['dashboard:default'])]
    public float $totalPendingAmount = 0.0;

    #[Groups(['dashboard:default'])]
    public array $activeUnits = [];

    #[Groups(['dashboard:default'])]
    public int $totalActiveUnits = 0;

    /**
     * Creates a DashboardResponse from an array of calculated data.
     */
    public static function fromData(array $data): self
    {
        $dto = new self();

        $dto->currentBalance = $data['currentBalance'] ?? 0.0;
        $dto->lastMonthBalance = $data['lastMonthBalance'] ?? 0.0;
        $dto->balancePercentChange = $data['balancePercentChange'] ?? null;

        $dto->currentMonthIncome = $data['currentMonthIncome'] ?? 0.0;
        $dto->previousMonthIncome = $data['previousMonthIncome'] ?? 0.0;
        $dto->incomePercentChange = $data['incomePercentChange'] ?? null;

        $dto->totalPendingItems = $data['totalPendingItems'] ?? 0;
        $dto->totalPendingAmount = $data['totalPendingAmount'] ?? 0.0;

        $dto->activeUnits = $data['activeUnits'] ?? [];

        $dto->totalActiveUnits = $data['totalActiveUnits'] ?? 0;

        return $dto;
    }
}
