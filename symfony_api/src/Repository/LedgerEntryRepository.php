<?php

namespace App\Repository;

use App\Entity\Building;
use App\Entity\LedgerEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LedgerEntry>
 */
class LedgerEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LedgerEntry::class);
    }

    public function getTotalIncomeByBuilding(int $buildingId): float
    {
        return (float) $this->createQueryBuilder('le')
            ->select('COALESCE(SUM(le.amount), 0)')
            ->where('le.building = :buildingId')
            ->andWhere('le.type = :type')
            ->andWhere('le.incomeType IN (:incomeTypes)')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('type', 'income')
            ->setParameter('incomeTypes', ['regular_contribution', 'special_assessment'])
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function getTotalExpensesByBuilding(int $buildingId): float
    {
        return (float) $this->createQueryBuilder('le')
            ->select('SUM(le.amount)')
            ->where('le.building = :buildingId')
            ->andWhere('le.type = :type')
            ->andWhere('le.expenseCategory IN (:expenseCategory)')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('type', 'expense')
            ->setParameter('expenseCategory', [
                'utilities', 'staff', 'syndic_fees', 'maintenance', 'administration', 'other'
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getCurrentMonthCashFlowByBuilding(int $buildingId): array
    {
        $startOfMonth = new \DateTime('first day of this month 00:00:00');
        $endOfMonth = new \DateTime('last day of this month 23:59:59');

        $result = $this->createQueryBuilder('le')
            ->select('
            SUM(CASE WHEN le.type = :income THEN le.amount ELSE 0 END) AS totalIncome,
            SUM(CASE WHEN le.type = :expense THEN le.amount ELSE 0 END) AS totalExpense
        ')
            ->where('le.building = :buildingId')
            ->andWhere('le.createdAt BETWEEN :start AND :end')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('income', 'income')
            ->setParameter('expense', 'expense')
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->getQuery()
            ->getSingleResult();

        $income = (float) ($result['totalIncome'] ?? 0);
        $expenses = (float) ($result['totalExpense'] ?? 0);

        return [
            'year' => (int) date('Y'),
            'month' => (int) date('n'),
            'totalIncome' => $income,
            'totalExpenses' => $expenses,
            'currentBalance' => $income - $expenses,
        ];
    }

    public function getFinancialSummaryLast6Months(Building $building): array
    {
        // Calculate date range for last 6 months
        $endDate = new \DateTime('last day of this month');
        $startDate = (clone $endDate)->modify('-5 months')->modify('first day of this month');

        $results = $this->createQueryBuilder('le')
            ->select([
                "YEAR(le.createdAt) as year",
                "MONTH(le.createdAt) as month",
                'SUM(CASE WHEN le.type = :income THEN le.amount ELSE 0 END) as income',
                'SUM(CASE WHEN le.type = :expense THEN le.amount ELSE 0 END) as expenses'
            ])
            ->where('le.building = :buildingId')
            ->andWhere('le.createdAt >= :startDate')
            ->andWhere('le.createdAt <= :endDate')
            ->groupBy('year, month')
            ->orderBy('year', 'ASC')
            ->addOrderBy('month', 'ASC')
            ->setParameter('buildingId', $building->getId())
            ->setParameter('startDate', $startDate->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'))
            ->setParameter('income', 'income')
            ->setParameter('expense', 'expense')
            ->getQuery()
            ->getArrayResult();

        return $this->transformToRechartsFormat($results, $startDate, $endDate);
    }

    private function transformToMonthlyFormat(array $results, \DateTime $startDate, \DateTime $endDate): array
    {
        // Create array with all months in the range, initialized with zero values
        $monthlyData = [];
        $current = clone $startDate;

        while ($current <= $endDate) {
            $key = $current->format('Y-n'); // Format: "2025-9" (year-month without leading zero)
            $monthlyData[$key] = [
                'year' => (int) $current->format('Y'),
                'month' => (int) $current->format('n'),
                'monthName' => $current->format('M'), // Short month name (Jan, Feb, etc.)
                'monthFullName' => $current->format('F'), // Full month name (January, February, etc.)
                'period' => $current->format('M Y'), // "Sep 2025"
                'income' => 0.0,
                'expenses' => 0.0,
                'net' => 0.0 // income - expenses
            ];
            $current->modify('+1 month');
        }

        // Fill in actual data from query results
        foreach ($results as $row) {
            $key = $row['year'] . '-' . $row['month'];
            if (isset($monthlyData[$key])) {
                $monthlyData[$key]['income'] = (float) $row['income'];
                $monthlyData[$key]['expenses'] = (float) $row['expenses'];
                $monthlyData[$key]['net'] = $monthlyData[$key]['income'] - $monthlyData[$key]['expenses'];
            }
        }

        return array_values($monthlyData);
    }

    private function transformToRechartsFormat(array $results, \DateTime $startDate, \DateTime $endDate): array
    {
        return $this->transformToMonthlyFormat($results, $startDate, $endDate);
    }

    public function getExpensesDistribution(Building $building): array
    {
        $results = $this->createQueryBuilder('le')
            ->select('le.expenseCategory AS name', 'SUM(le.amount) AS value')
            ->where('le.building = :buildingId')
            ->andWhere('le.type = :type')
            ->setParameter('buildingId', $building->getId())
            ->setParameter('type', 'expense')
            ->groupBy('le.expenseCategory')
            ->getQuery()
            ->getArrayResult();

        $expensesDistribution = array_map(function ($item) {
            return [
                'name' => $item['name'],
                'value' => (float) $item['value']
            ];
        }, $results);

        return  $expensesDistribution;
    }
}
