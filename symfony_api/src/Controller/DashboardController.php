<?php

namespace App\Controller;

use App\Service\DashboardService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/dashboard', name: 'api_dashboard_')]
final class DashboardController extends AbstractApiController
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    #[Route('/building/{buildingId}', methods: ['GET'], name: 'cards')]
    public function index(int $buildingId): Response
    {
        $stats = $this->dashboardService->getCardStats($buildingId);

        return new JsonResponse([
            'currentBalance' => $stats->currentBalance,
            'lastMonthBalance' => $stats->lastMonthBalance,
            'balancePercentChange' => $stats->balancePercentChange,
            'currentMonthIncome' => $stats->currentMonthIncome,
            'previousMonthIncome' => $stats->previousMonthIncome,
            'incomePercentChange' => $stats->incomePercentChange,
            'totalPendingItems' => $stats->totalPendingItems,
            'totalPendingAmount' => $stats->totalPendingAmount,
            'activeUnits' => $stats->activeUnits,
        ]);
    }
}
