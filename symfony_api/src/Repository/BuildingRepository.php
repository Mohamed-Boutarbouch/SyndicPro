<?php

namespace App\Repository;

use App\Entity\Building;
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
}
