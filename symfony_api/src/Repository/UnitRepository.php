<?php

namespace App\Repository;

use App\Entity\Unit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Unit>
 */
class UnitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Unit::class);
    }

    /**
     * Get contribution overview for each unit per building
     */
    public function getContributionOverviewByBuilding(int $buildingId): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select(
                'u.id AS unitId',
                'u.number AS unitNumber',
                "CONCAT(usr.firstName, ' ', usr.lastName) AS ownerName",
                'cs.frequency',
                'cs.amountPerPayment AS amountPerPayment',
                'cs.nextDueDate AS nextDueDate',
                'MAX(p.date) AS lastPayment',
                'COALESCE(SUM(p.amount), 0) AS totalPaid'
            )
            ->join('u.user', 'usr')
            ->join('App\Entity\ContributionSchedule', 'cs', 'WITH', 'cs.unit = u AND cs.isActive = true')
            ->leftJoin('App\Entity\Payment', 'p', 'WITH', 'p.contributionSchedule = cs')
            ->where('u.building = :buildingId')
            ->setParameter('buildingId', $buildingId)
            ->groupBy('u.id, cs.id, u.number, usr.firstName, usr.lastName, cs.frequency, cs.amountPerPayment, cs.nextDueDate');

        $results = $qb->getQuery()->getArrayResult();

        // Format the results in PHP
        foreach ($results as &$row) {
            // Convert amountPerPayment to float
            $row['amountPerPayment'] = (float) $row['amountPerPayment'];

            // Format frequency - convert enum to string
            if ($row['frequency'] instanceof \App\Enum\ContributionFrequency) {
                $row['frequency'] = $row['frequency']->value;
            } else {
                $row['frequency'] = (string) $row['frequency'];
            }

            // Format nextDueDate
            if ($row['nextDueDate'] instanceof \DateTimeInterface) {
                $row['nextDueDate'] = $row['nextDueDate']->format('Y-m-d');
            } elseif (is_string($row['nextDueDate'])) {
                // If it's already a string, parse and reformat
                $row['nextDueDate'] = (new \DateTime($row['nextDueDate']))->format('Y-m-d');
            } else {
                $row['nextDueDate'] = null;
            }

            // Format lastPayment
            if ($row['lastPayment'] instanceof \DateTimeInterface) {
                $row['lastPayment'] = $row['lastPayment']->format('Y-m-d');
            } elseif (is_string($row['lastPayment'])) {
                // If it's already a string, parse and reformat
                $row['lastPayment'] = (new \DateTime($row['lastPayment']))->format('Y-m-d');
            } else {
                $row['lastPayment'] = null;
            }

            // Convert totalPaid to float
            $row['totalPaid'] = (float) $row['totalPaid'];
        }

        return $results; // Return formatted arrays, not DTOs
    }

    public function findByIdAndBuildingId(int $unitId, int $buildingId): ?array
    {
        return $this->createQueryBuilder('u')
            ->select([
                'u.id',
                'u.number',
                'u.type',
                'u.floor',
                'b.id as buildingId',
            ])
            ->join('u.building', 'b')
            ->where('u.id = :unitId')
            ->andWhere('b.id = :buildingId')
            ->setParameter('unitId', $unitId)
            ->setParameter('buildingId', $buildingId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
