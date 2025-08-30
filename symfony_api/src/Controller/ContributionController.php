<?php

namespace App\Controller;

use App\DTO\Response\UnitContributionResponse;
use App\Repository\UnitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/contributions', name: 'api_contributions_')]
final class ContributionController extends AbstractController
{
    public function __construct(
        private UnitRepository $unitRepository,
    ) {
    }

    #[Route('/building/{buildingId}/schedule', methods: ['GET'], name: 'schedule')]
    public function cards(int $buildingId): Response
    {
        $stats = $this->unitRepository->getContributionOverviewByBuilding($buildingId);

        // Convert array of arrays to array of DTOs
        $response = UnitContributionResponse::fromDataArray($stats);

        return $this->json(
            $response,
            200,
            [],
            ['groups' => ['contribution:overview']]
        );
    }
}
