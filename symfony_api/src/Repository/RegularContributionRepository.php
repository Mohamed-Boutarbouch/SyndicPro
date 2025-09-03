<?php

namespace App\Repository;

use App\Entity\RegularContribution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RegularContribution>
 */
class RegularContributionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegularContribution::class);
    }

    public function findOneScheduleByUnitId(int $unitId): ?RegularContribution
    {
        return $this->createQueryBuilder('rc')
            ->andWhere('rc.unit = :unitId')
            ->setParameter('unitId', $unitId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findRegularContributionReport(int $buildingId, int $year): ?array
    {
        $result = $this->createQueryBuilder('rc')
            ->select([
                'b.id as buildingId',
                'b.name as buildingName',
                'rc.year as paymentYear',
                'rc.id as regularContributionId',
                'rc.totalAnnualAmount as totalAnnualAmount',
                'rc.startDate as periodStartDate',
                'rc.endDate as periodEndDate',
                'rc.amountPerUnit',
                'COALESCE(SUM(le.amount), 0.00) as totalPaidAmount',
                'COUNT(le.id) as totalPayments'
            ])
            ->join('rc.building', 'b')
            ->leftJoin('rc.contributionSchedules', 'cs')
            ->leftJoin(
                'App\Entity\LedgerEntry',
                'le',
                'WITH',
                'le.building = rc.building AND le.incomeType = :incomeType AND le.contributionSchedule = cs'
            )
            ->where('rc.building = :buildingId')
            ->andWhere('rc.year = :year')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('year', $year)
            ->setParameter('incomeType', 'regular_contribution')
            ->groupBy('rc.id, b.id, b.name, rc.year, rc.totalAnnualAmount, rc.startDate')
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }
}
