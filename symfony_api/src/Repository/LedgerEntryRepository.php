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

    public function getMonthlyCashFlowByBuilding(int $buildingId): array
    {
        $results = $this->createQueryBuilder('le')
            ->select('YEAR(le.createdAt) AS year, MONTH(le.createdAt) AS month')
            ->addSelect('SUM(CASE WHEN le.type = :income THEN le.amount ELSE 0 END) AS totalIncome')
            ->addSelect('SUM(CASE WHEN le.type = :expense THEN le.amount ELSE 0 END) AS totalExpense')
            ->where('le.building = :buildingId')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('income', 'income')
            ->setParameter('expense', 'expense')
            ->groupBy('year, month')
            ->orderBy('year, month')
            ->getQuery()
            ->getArrayResult();

        foreach ($results as &$row) {
            $row['totalIncome'] = (float) $row['totalIncome'];
            $row['totalExpense'] = (float) $row['totalExpense'];
        }

        return $results;
    }
}
