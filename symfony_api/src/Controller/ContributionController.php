<?php

namespace App\Controller;

use App\DTO\Response\UnitContributionResponse;
use App\Entity\Building;
use App\Repository\RegularContributionRepository;
use App\Repository\UnitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/contributions', name: 'api_contributions_')]
final class ContributionController extends AbstractController
{
    public function __construct(
        private UnitRepository $unitRepository,
        private RegularContributionRepository $regularContributionRepository,
    ) {
    }

    #[Route('/building/{buildingId}/stats/year/{year}', methods: ['GET'], name: 'stats')]
    public function stats(int $buildingId, int $year): Response
    {
        $stats = $this->regularContributionRepository->getRegularContributionSummary($buildingId, $year);

        return $this->json(
            UnitContributionResponse::fromData($stats),
            status: 200,
            context: ['groups' => ['contribution:card-stats']]
        );
    }

    #[Route('/building/{buildingId}/schedule/year/{year}', methods: ['GET'], name: 'schedule')]
    public function schedule(int $buildingId, int $year): Response
    {
        $schedule = $this->regularContributionRepository->findRegularContributionReport($buildingId, $year);

        return $this->json(
            UnitContributionResponse::fromDataArray($schedule),
            status: 200,
            context: ['groups' => ['contribution:schedule-table']]
        );
    }

    #[Route('/{id}/history/{year}', methods: ['GET'], name: 'history')]
    public function history(Building $building, int $year): Response
    {
        $history = $this->regularContributionRepository->findRecentPaymentHistory($building, $year);

        return $this->json(
            UnitContributionResponse::fromDataArray($history),
            status: 200,
            context: ['groups' => ['contribution:history-table']]
        );
    }
}
