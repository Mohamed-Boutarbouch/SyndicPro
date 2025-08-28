<?php

namespace App\DTO\Response;

class DashboardResponse
{
    public float $currentBalance = 0;
    public float $lastMonthBalance = 0;
    public ?float $balancePercentChange = null;

    public float $currentMonthIncome = 0;
    public float $previousMonthIncome = 0;
    public ?float $incomePercentChange = null;

    public int $totalPendingItems = 0;
    public float $totalPendingAmount = 0;

    public array $activeUnits = [];

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
