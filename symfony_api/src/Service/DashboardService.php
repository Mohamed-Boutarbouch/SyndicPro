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

    public function getCardStats(int $buildingId): array
    {
        return $this->buildingRepository->getBuildingCardStats($buildingId);
    }

    public function getIncomeExpenses(int $buildingId): array
    {
        return $this->buildingRepository->getFinancialSummaryLast6Months($buildingId);
    }
}
