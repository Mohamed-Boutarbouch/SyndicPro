<?php

namespace App\DTO\Response;

use App\Enum\ExpenseCategory;
use Symfony\Component\Serializer\Annotation\Groups;

class DashboardResponse
{
    #[Groups(['dashboard:card'])]
    public float $currentBalance = 0.0;

    #[Groups(['dashboard:card'])]
    public float $lastMonthBalance = 0.0;

    #[Groups(['dashboard:card'])]
    public ?float $balancePercentChange = null;

    #[Groups(['dashboard:card'])]
    public float $currentMonthIncome = 0.0;

    #[Groups(['dashboard:card'])]
    public float $previousMonthIncome = 0.0;

    #[Groups(['dashboard:card'])]
    public ?float $incomePercentChange = null;

    #[Groups(['dashboard:card'])]
    public int $totalPendingItems = 0;

    #[Groups(['dashboard:card'])]
    public float $totalPendingAmount = 0.0;

    #[Groups(['dashboard:card'])]
    public array $activeUnits = [];

    #[Groups(['dashboard:card'])]
    public int $totalActiveUnits = 0;

    #[Groups(['dashboard:income-expenses'])]
    public array $monthlyIncomeExpenses = [];

    #[Groups(['dashboard:expenses-distribution'])]
    public array $expensesDistribution = [];

    /**
     * Get display name for an expense category
     */
    private function getExpenseDisplayName(ExpenseCategory $category): string
    {
        return match ($category) {
            ExpenseCategory::COMMON_AREA => 'Maintenance',
            ExpenseCategory::WATER_ELECTRICITY => 'Utilities',
            ExpenseCategory::PERSONNEL => 'Personnel',
            ExpenseCategory::SYNDIC_ADMIN => 'Administration',
            ExpenseCategory::SYNDIC_REMUNERATION => 'Remuneration',
            ExpenseCategory::OTHER => 'Other',
        };
    }

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
        $dto->monthlyIncomeExpenses = $data['monthlyIncomeExpenses'] ?? [];

        //$dto->expensesDistribution = $data['expensesDistribution'] ?? [];
        // Transform expenses distribution with proper naming
        $dto->expensesDistribution = $dto->transformExpensesDistribution($data['expensesDistribution'] ?? []);

        return $dto;
    }

    /**
     * Transform expenses distribution array to use display names
     */
    private function transformExpensesDistribution(array $expenses): array
    {
        $transformed = [];

        foreach ($expenses as $expense) {
            $originalName = $expense['name'] ?? '';

            // Determine display name
            if ($originalName instanceof ExpenseCategory) {
                $displayName = $this->getExpenseDisplayName($originalName);
            } else {
                // Try to convert string to enum
                $category = ExpenseCategory::tryFrom($originalName);
                $displayName = $category ? $this->getExpenseDisplayName($category) : $originalName;
            }

            $transformed[] = [
                'name' => $displayName,
                'value' => (float) ($expense['value'] ?? 0.0)
            ];
        }

        return $transformed;
    }

    /**
     * Get the display name for an expense type by string value
     */
    public static function getExpenseDisplayNameByString(string $internalName): string
    {
        $category = ExpenseCategory::tryFrom($internalName);
        return $category ? (new self())->getExpenseDisplayName($category) : $internalName;
    }
}
