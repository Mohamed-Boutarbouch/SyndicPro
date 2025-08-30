<?php

namespace App\Service;

use App\Repository\BuildingRepository;
use Doctrine\ORM\EntityManagerInterface;

class DashboardService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BuildingRepository $buildingRepository,
    ) {
    }

    /**
     * Get card statistics for a specific building
     */
    public function getCardStats(int $buildingId): array
    {
        return $this->buildingRepository->getBuildingCardStats($buildingId);
    }

    /**
     * Get income and expenses data for the last 6 months
     */
    public function getIncomeExpenses(int $buildingId): array
    {
        return $this->buildingRepository->getFinancialSummaryLast6Months($buildingId);
    }

    /**
     * Get expenses distribution by category for a specific building
     */
    public function getExpensesDistribution(int $buildingId): array
    {
        return $this->buildingRepository->getExpensesDistributionByBuilding($buildingId);
    }
}
