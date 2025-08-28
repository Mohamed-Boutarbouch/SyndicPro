<?php

namespace App\Service;

use App\DTO\Response\DashboardResponse;
use App\Repository\BuildingRepository;
use Doctrine\ORM\EntityManagerInterface;

class DashboardService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BuildingRepository $buildingRepository,
    ) {
    }

    public function getCardStats(int $buildingId): DashboardResponse
    {
        return $this->buildingRepository->getBuildingCardStats($buildingId);
    }
}
