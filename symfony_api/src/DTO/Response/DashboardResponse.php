<?php

namespace App\DTO\Response;

use App\Enum\LedgerEntryExpenseCategory;
use Symfony\Component\Serializer\Annotation\Groups;

class DashboardResponse
{
    #[Groups(['dashboard:card'])]
    public float $totalIncome;

    #[Groups(['dashboard:card'])]
    public float $totalExpenses;

    #[Groups(['dashboard:card'])]
    public float $actualBalance;

    #[Groups(['dashboard:card'])]
    public array $currentMonthCashFlow = [];

    #[Groups(['dashboard:card'])]
    public array $activeUnits = [];

    #[Groups(['dashboard:card'])]
    public int $activeApartments;

    #[Groups(['dashboard:card'])]
    public int $activeCommercialUnits;

    #[Groups(['dashboard:income-expenses'])]
    public array $monthlyIncomeExpenses = [];

    #[Groups(['dashboard:expenses-distribution'])]
    public array $expensesDistribution = [];

    /**
     * Creates a DashboardResponse from an array of calculated data.
     */
    public static function fromData(array $data): self
    {
        $dto = new self();

        $dto->totalIncome = $data['totalIncome'] ?? 0;
        $dto->totalExpenses = $data['totalExpenses'] ?? 0;
        $dto->actualBalance = $data['actualBalance'] ?? 0;
        $dto->currentMonthCashFlow = $data['currentMonthCashFlow'] ?? [];
        $dto->activeUnits = $data['activeUnits'] ?? [];
        $dto->activeApartments = $data['activeApartments'] ?? 0;
        $dto->activeCommercialUnits = $data['activeCommercialUnits'] ?? 0;

        $dto->monthlyIncomeExpenses = $data['monthlyIncomeExpenses'] ?? [];

        //$dto->expensesDistribution = $data['expensesDistribution'] ?? [];
        // Transform expenses distribution with proper naming
        $dto->expensesDistribution = $dto->transformExpensesDistribution($data['expensesDistribution'] ?? []);

        return $dto;
    }

    /**
     * Get display name for an expense category
     */
    private function getExpenseDisplayName(LedgerEntryExpenseCategory $category): string
    {
        return match ($category) {
            LedgerEntryExpenseCategory::MAINTENANCE => 'Maintenance',
            LedgerEntryExpenseCategory::UTILITIES => 'Utilities',
            LedgerEntryExpenseCategory::STAFF => 'Personnel',
            LedgerEntryExpenseCategory::ADMINISTRATION => 'Administration',
            LedgerEntryExpenseCategory::SYNDIC_FEES => 'Remuneration',
            LedgerEntryExpenseCategory::OTHER => 'Other',
        };
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
            if ($originalName instanceof LedgerEntryExpenseCategory) {
                $displayName = $this->getExpenseDisplayName($originalName);
            } else {
                // Try to convert string to enum
                $category = LedgerEntryExpenseCategory::tryFrom($originalName);
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
        $category = LedgerEntryExpenseCategory::tryFrom($internalName);
        return $category ? (new self())->getExpenseDisplayName($category) : $internalName;
    }
}
