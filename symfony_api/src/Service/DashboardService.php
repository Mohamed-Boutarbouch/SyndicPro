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
     * Get expenses distribution by category for a specific building
     */
    public function getExpensesDistribution(int $buildingId): array
    {
        return $this->buildingRepository->getExpensesDistributionByBuilding($buildingId);
    }
}
