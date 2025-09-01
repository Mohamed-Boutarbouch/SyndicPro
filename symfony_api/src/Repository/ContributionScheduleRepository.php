<?php

namespace App\Repository;

use App\Entity\ContributionSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContributionSchedule>
 */
class ContributionScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContributionSchedule::class);
    }

    public function findByRegularContributionAndUnit(int $regularContributionId, int $unitId): ?ContributionSchedule
    {
        return $this->createQueryBuilder('cs')
            ->andWhere('cs.regularContribution = :rcId')
            ->andWhere('cs.unit = :unitId')
            ->setParameter('rcId', $regularContributionId)
            ->setParameter('unitId', $unitId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
