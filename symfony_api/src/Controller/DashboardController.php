<?php

namespace App\Controller;

use App\DTO\Response\DashboardResponse;
use App\Entity\Building;
use App\Repository\BuildingRepository;
use App\Repository\LedgerEntryRepository;
use App\Repository\UnitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/dashboard', name: 'api_dashboard_')]
final class DashboardController extends AbstractController
{
    public function __construct(
        private BuildingRepository $buildingRepository,
        private UnitRepository $unitRepository,
        private LedgerEntryRepository $ledgerEntryRepository
    ) {
    }

    /**
     * Get card statistics for a specific building
     */
    #[Route('/{buildingId}/cards', methods: ['GET'], name: 'cards')]
    public function cards(int $buildingId): Response
    {
        $totalIncome = $this->ledgerEntryRepository->getTotalIncomeByBuilding($buildingId);
        $totalExpenses = $this->ledgerEntryRepository->getTotalExpensesByBuilding($buildingId);
        $currentMonthCashFlow = $this->ledgerEntryRepository->getCurrentMonthCashFlowByBuilding($buildingId);

        $activeUnits = $this->unitRepository->getActiveUnitsByBuilding($buildingId);
        $activeApartments = $this->unitRepository->getActiveUnitsByBuildingAndType($buildingId, 'apartment');
        $activeCommercialUnits = $this
            ->unitRepository
            ->getActiveUnitsByBuildingAndType($buildingId, 'commercial_local');

        $stats = [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'actualBalance' => $totalIncome - $totalExpenses,
            'currentMonthCashFlow' => $currentMonthCashFlow,
            'activeUnits' => $activeUnits,
            'activeApartments' => $activeApartments,
            'activeCommercialUnits' => $activeCommercialUnits,
        ];

        return $this->json(
            DashboardResponse::fromData($stats),
            status: 200,
            context: ['groups' => ['dashboard:card']]
        );
    }

    #[Route('/{id}/income-expenses', methods: ['GET'], name: 'income_expenses')]
    public function incomeAndExpenses(Building $building): Response
    {
        $monthlyData = $this->ledgerEntryRepository->getFinancialSummaryLast6Months($building);

        return $this->json($monthlyData, status: 200);
    }

    #[Route('/{id}/expenses-distribution', methods: ['GET'], name: 'expenses_distribution')]
    public function expensesDistribution(Building $building): Response
    {
        return $this->json(
            $this->ledgerEntryRepository->getExpensesDistribution($building),
            status: 200
        );
    }

    #[Route('/{id}/transactions', methods: ['GET'], name: 'transactions')]
    public function transactions(Building $building): Response
    {
        $entries = $this->ledgerEntryRepository->findBy(
            ['building' => $building],
            ['createdAt' => 'DESC']
        );

        $data = array_map(function ($entry) {
            return [
                'id' => $entry->getId(),
                'amount' => $entry->getAmount(),
                'description' => $entry->getDescription(),
                'type' => $entry->getType()?->value,
                'incomeType' => $entry->getIncomeType()?->value,
                'expenseCategory' => $entry->getExpenseCategory()?->value,
                'vendor' => $entry->getVendor(),
                'unit' => $entry->getUnit() ? [
                    'id' => $entry->getUnit()->getId(),
                    'number' => $entry->getUnit()->getNumber(),
                ] : null,
                'referenceNumber' => $entry->getReferenceNumber(),
                'paymentMethod' => $entry->getPaymentMethod()?->value,
                'createdAt' => $entry->getCreatedAt()?->format('Y-m-d H:i:s'),
                'updatedAt' => $entry->getUpdatedAt()?->format('Y-m-d H:i:s'),
            ];
        }, $entries);

        return $this->json($data);
    }
}
