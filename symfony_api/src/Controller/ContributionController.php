<?php

namespace App\Controller;

use App\DTO\Response\UnitContributionResponse;
use App\Repository\BuildingRepository;
use App\Repository\UnitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/contributions', name: 'api_contributions_')]
final class ContributionController extends AbstractController
{
    public function __construct(
        private UnitRepository $unitRepository,
        private BuildingRepository $buildingRepository,
    ) {
    }

    #[Route('/building/{buildingId}/stats/year/{year}', methods: ['GET'], name: 'stats')]
    public function stats(int $buildingId, int $year): Response
    {
        $stats = $this->buildingRepository->getBuildingContributionPaymentSummary($buildingId, $year);

        return $this->json(
            UnitContributionResponse::fromData($stats),
            status: 200,
            context: ['groups' => ['contribution:stats']]
        );
    }

    #[Route('/building/{buildingId}/schedule', methods: ['GET'], name: 'schedule')]
    public function schedule(int $buildingId): Response
    {
        $stats = $this->unitRepository->getContributionOverviewByBuilding($buildingId);

        return $this->json(
            UnitContributionResponse::fromDataArray($stats),
            status: 200,
            context: ['groups' => ['contribution:overview']]
        );
    }
}
