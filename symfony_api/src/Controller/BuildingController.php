<?php

namespace App\Controller;

use App\DTO\Response\ResidentsResponse;
use App\Repository\BuildingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/buildings', name: 'api_buildings_')]
final class BuildingController extends AbstractController
{
    public function __construct(
        private BuildingRepository $buildingRepository,
    ) {
    }

    #[Route('/{buildingId}/residents', methods: ['GET'], name: 'residents')]
    public function residents(int $buildingId): Response
    {
        $stats = $this->buildingRepository->getResidentsByBuilding($buildingId);

        $response = ResidentsResponse::fromDataArray($stats);

        return $this->json(
            $response,
            200,
            [],
            ['groups' => ['form']]
        );
    }
}
