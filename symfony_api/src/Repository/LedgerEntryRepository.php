<?php

namespace App\Repository;

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
}
