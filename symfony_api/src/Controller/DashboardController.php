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

    #[Route('/building/{buildingId}/cards', methods: ['GET'], name: 'cards')]
    public function cards(int $buildingId): Response
    {

        // echo json_encode($this->dashboardService->getCardStats($buildingId));
        // die();
        $stats = $this->dashboardService->getCardStats($buildingId);

        return $this->json(
            DashboardResponse::fromData($stats),
            200,
            [],
            ['groups' => ['dashboard:card']]
        );
    }

    #[Route('/building/{buildingId}/income-expenses', methods: ['GET'], name: 'income_expenses')]
    public function incomeAndExpenses(int $buildingId): Response
    {
        $stats = $this->dashboardService->getIncomeExpenses($buildingId);

        return $this->json(
            DashboardResponse::fromData($stats),
            200,
            [],
            ['groups' => ['dashboard:income-expenses']]
        );
    }
}
