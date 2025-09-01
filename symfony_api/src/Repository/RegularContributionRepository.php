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
            ->getOneOrNullResult(); // returns one entity or null
    }
}
