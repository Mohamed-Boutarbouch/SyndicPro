<?php

namespace App\Repository;

use App\Entity\Building;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Building>
 */
class BuildingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Building::class);
    }

    public function getBuildingCardStats(int $buildingId): array
    {
        $em = $this->getEntityManager();

        // --- Total and last month balance ---
        $currentBalance = (float) $em->createQueryBuilder()
            ->select('SUM(CASE WHEN t.type = \'income\' THEN t.amount ELSE -t.amount END)')
            ->from('App\Entity\Transaction', 't')
            ->where('t.building = :buildingId')
            ->andWhere('t.status = :status')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('status', 'approved')
            ->getQuery()
            ->getSingleScalarResult();

        $lastMonthBalance = (float) $em->createQueryBuilder()
            ->select('SUM(CASE WHEN t.type = \'income\' THEN t.amount ELSE -t.amount END)')
            ->from('App\Entity\Transaction', 't')
            ->where('t.building = :buildingId')
            ->andWhere('t.status = :status')
            ->andWhere('t.date >= :firstDayLastMonth')
            ->andWhere('t.date < :firstDayThisMonth')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('status', 'approved')
            ->setParameter('firstDayLastMonth', (new \DateTime('first day of last month'))->format('Y-m-d'))
            ->setParameter('firstDayThisMonth', (new \DateTime('first day of this month'))->format('Y-m-d'))
            ->getQuery()
            ->getSingleScalarResult();

        $balancePercentChange = $lastMonthBalance ? (($currentBalance - $lastMonthBalance) / $lastMonthBalance) * 100 : null;

        // --- Monthly income ---
        $currentStart = new \DateTime('first day of this month');
        $currentEnd = new \DateTime('last day of this month');
        $previousStart = new \DateTime('first day of last month');
        $previousEnd = new \DateTime('last day of last month');

        $incomeResult = $em->createQueryBuilder()
            ->select('
            SUM(CASE WHEN t.date BETWEEN :currentStart AND :currentEnd THEN t.amount ELSE 0 END) AS currentMonthIncome,
            SUM(CASE WHEN t.date BETWEEN :previousStart AND :previousEnd THEN t.amount ELSE 0 END) AS previousMonthIncome
        ')
            ->from('App\Entity\Transaction', 't')
            ->where('t.building = :buildingId')
            ->andWhere('t.type = :type')
            ->andWhere('t.status = :status')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('type', 'income')
            ->setParameter('status', 'approved')
            ->setParameter('currentStart', $currentStart->format('Y-m-d 00:00:00'))
            ->setParameter('currentEnd', $currentEnd->format('Y-m-d 23:59:59'))
            ->setParameter('previousStart', $previousStart->format('Y-m-d 00:00:00'))
            ->setParameter('previousEnd', $previousEnd->format('Y-m-d 23:59:59'))
            ->getQuery()
            ->getOneOrNullResult();

        $currentMonthIncome = (float) ($incomeResult['currentMonthIncome'] ?? 0);
        $previousMonthIncome = (float) ($incomeResult['previousMonthIncome'] ?? 0);
        $incomePercentChange = $previousMonthIncome > 0 ? round((($currentMonthIncome - $previousMonthIncome) / $previousMonthIncome) * 100, 1) : null;

        // --- Pending assessments ---
        $pendingResult = $em->createQueryBuilder()
            ->select([
                'COUNT(ai.id) AS totalPendingItems',
                'SUM(ai.amount) AS totalPendingAmount'
            ])
            ->from('App\Entity\AssessmentItem', 'ai')
            ->innerJoin('ai.assessment', 'a')
            ->innerJoin('a.building', 'b')
            ->where('b.id = :buildingId')
            ->andWhere('ai.status IN (:statuses)')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('statuses', ['pending', 'overdue', 'partial'])
            ->getQuery()
            ->getOneOrNullResult();

        $totalPendingItems = (int) ($pendingResult['totalPendingItems'] ?? 0);
        $totalPendingAmount = (float) ($pendingResult['totalPendingAmount'] ?? 0);

        // --- Active units ---
        $activeUnits = $em->createQueryBuilder()
            ->select('u.type, COUNT(u.id) AS count')
            ->from('App\Entity\Unit', 'u')
            ->where('u.building = :buildingId')
            ->andWhere('u.user IS NOT NULL')
            ->andWhere('u.type IN (:types)')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('types', ['apartment', 'commercial_local'])
            ->groupBy('u.type')
            ->getQuery()
            ->getArrayResult();

        $totalActiveUnits = array_sum(array_column($activeUnits, 'count'));

        return [
            'currentBalance' => $currentBalance,
            'lastMonthBalance' => $lastMonthBalance,
            'balancePercentChange' => $balancePercentChange,
            'currentMonthIncome' => $currentMonthIncome,
            'previousMonthIncome' => $previousMonthIncome,
            'incomePercentChange' => $incomePercentChange,
            'totalPendingItems' => $totalPendingItems,
            'totalPendingAmount' => $totalPendingAmount,
            'activeUnits' => $activeUnits,
            'totalActiveUnits' => $totalActiveUnits
        ];
    }

    /**
     * Get financial summary for the last 6 months
     */
    public function getFinancialSummaryLast6Months(int $buildingId): array
    {

        $em = $this->getEntityManager();

        // $endDate = new \DateTime('last day of this month');
        // $startDate = (clone $endDate)->modify('-5 months')->modify('first day of this month');

        $currentYear = (int) date('Y');
        $startDate = new \DateTime("$currentYear-01-01"); // January 1st
        $endDate   = new \DateTime("$currentYear-06-30"); // June 30th

        $results = $em->createQueryBuilder()
            ->select([
                "SUBSTRING(t.date, 1, 4) as year",  // first 4 chars = year
                "SUBSTRING(t.date, 6, 2) as month", // chars 6-7 = month
                'SUM(CASE WHEN t.type = :income AND t.status = :approved THEN t.amount ELSE 0 END) as income',
                'SUM(CASE WHEN t.type = :expense AND t.status IN (:expenseStatuses) THEN t.amount ELSE 0 END) as expenses'
            ])
            ->from('App\Entity\Transaction', 't')
            ->where('t.building = :buildingId')
            ->andWhere('t.date >= :startDate')
            ->andWhere('t.date <= :endDate')
            ->groupBy('year, month')
            ->orderBy('year', 'ASC')
            ->addOrderBy('month', 'ASC')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('startDate', $startDate->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'))
            ->setParameter('income', 'income')
            ->setParameter('expense', 'expense')
            ->setParameter('approved', 'approved')
            ->setParameter('expenseStatuses', ['approved', 'paid'])
            ->getQuery()
            ->getArrayResult();

        return $this->transformToMonthlyFormat($results, $startDate, $endDate);
    }

    /**
     * Transform database results to monthly format with month names
     */
    private function transformToMonthlyFormat(array $results, \DateTime $startDate, \DateTime $endDate): array
    {
        $monthlyData = [];
        $currentDate = clone $startDate;

        // Create array with all months in range, using leading zero
        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m'); // e.g., "2025-01"
            $monthlyData[$monthKey] = [
                'month' => $currentDate->format('F'),
                'income' => 0.0,
                'expenses' => 0.0
            ];
            $currentDate->modify('first day of next month');
        }

        // Fill in actual data from query results
        foreach ($results as $row) {
            $monthKey = $row['year'] . '-' . $row['month']; // e.g., "2025-01"
            if (isset($monthlyData[$monthKey])) {
                $monthlyData[$monthKey]['income'] = (float) $row['income'];
                $monthlyData[$monthKey]['expenses'] = (float) $row['expenses'];
            }
        }

        return ['monthlyIncomeExpenses' => array_values($monthlyData)];
    }

    public function getExpensesDistributionByBuilding(int $buildingId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('t.expense_category AS name', 'SUM(t.amount) AS value')
            ->from(Transaction::class, 't')
            ->where('t.building = :buildingId')
            ->andWhere('t.type = :type')
            ->andWhere($qb->expr()->in('t.status', ':statuses'))
            ->setParameter('buildingId', $buildingId)
            ->setParameter('type', 'expense')
            ->setParameter('statuses', ['approved', 'paid'])
            ->groupBy('t.expense_category');

        $results = $qb->getQuery()->getArrayResult();

        $expensesDistribution = array_map(function ($item) {
            return [
                'name' => $item['name'],
                'value' => (float) $item['value']
            ];
        }, $results);

        return ['expensesDistribution' => $expensesDistribution];
    }

    public function getResidentsByBuilding(int $buildingId): array
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.units', 'u')
            ->innerJoin('u.user', 'usr')
            ->where('b.id = :buildingId')
            ->setParameter('buildingId', $buildingId)
            ->select('usr.id AS userId, usr.firstName, usr.lastName, usr.email, u.id AS unitId, u.number, u.type AS unitType')
            ->getQuery()
            ->getArrayResult();
    }
}
