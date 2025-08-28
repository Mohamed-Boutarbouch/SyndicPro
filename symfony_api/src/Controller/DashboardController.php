<?php

namespace App\Controller;

use App\DTO\Response\DashboardResponse;
use App\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/dashboard', name: 'api_dashboard_')]
final class DashboardController extends AbstractController
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
    }

    #[Route('/building/{buildingId}', methods: ['GET'], name: 'cards')]
    public function index(int $buildingId): Response
    {
        $stats = $this->dashboardService->getCardStats($buildingId);

        return $this->json(
            DashboardResponse::fromData($stats),
            200,
            [],
            ['groups' => ['dashboard:default']]
        );
    }
}
