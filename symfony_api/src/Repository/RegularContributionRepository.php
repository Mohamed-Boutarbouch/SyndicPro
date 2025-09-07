<?php

namespace App\Repository;

use App\Entity\Building;
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

    public function getRegularContributionSummary(int $buildingId, int $year): ?array
    {
        return $this->createQueryBuilder('rc')
            ->select([
                'b.id AS buildingId',
                'b.name AS buildingName',
                'rc.year AS paymentYear',
                'rc.amountPerUnit',
                'rc.id AS regularContributionId',
                'rc.totalAnnualAmount AS totalAnnualAmount',
                'rc.startDate AS periodStartDate',
                'rc.endDate AS periodEndDate',
                'COALESCE(SUM(le.amount), 0) AS totalPaidAmount',
                'COUNT(le.id) AS totalPaymentCount'
            ])
            ->join('rc.building', 'b')
            ->leftJoin('rc.contributionSchedules', 'cs')
            ->leftJoin(
                'cs.ledgerEntries',
                'le',
                'WITH',
                'le.type = :incomeType AND le.incomeType = :regularContribution'
            )
            ->where('rc.building = :buildingId')
            ->andWhere('rc.year = :year')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('year', $year)
            ->setParameter('incomeType', 'income')
            ->setParameter('regularContribution', 'regular_contribution')
            ->groupBy('rc.id, b.id')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findRegularContributionReport(int $buildingId, int $year): array
    {
        return $this->createQueryBuilder('rc')
            ->select([
                'b.id AS buildingId',
                'rc.id AS regularContributionId',
                'us.id AS ownerId',
                'us.firstName AS ownerFirstName',
                'us.lastName AS ownerLastName',
                // schedule-specific
                'cs.id AS scheduleId',
                'un.id AS unitId',
                'un.number AS unitNumber',
                'un.floor AS unitFloor',
                'cs.amountPerPayment AS amountPerPayment',
                'cs.nextDueDate AS nextDueDate',
                'cs.frequency AS frequency',
                // payments info
                'COALESCE(SUM(le.amount), 0.00) AS actualPaidAmountPerUnit'
            ])
            ->join('rc.building', 'b')
            ->leftJoin('rc.contributionSchedules', 'cs')
            ->leftJoin('cs.unit', 'un')
            ->leftJoin('un.user', 'us')
            ->leftJoin(
                'App\Entity\LedgerEntry',
                'le',
                'WITH',
                'le.contributionSchedule = cs AND le.incomeType = :incomeType'
            )
            ->where('rc.building = :buildingId')
            ->andWhere('rc.year = :year')
            ->setParameter('buildingId', $buildingId)
            ->setParameter('year', $year)
            ->setParameter('incomeType', 'regular_contribution')
            ->groupBy(
                'rc.id, b.id, b.name, rc.year, rc.totalAnnualAmount, rc.startDate, rc.endDate,
         cs.id, un.id, un.number, un.floor, cs.amountPerPayment, cs.nextDueDate, cs.frequency'
            )
            ->orderBy('un.number', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function findRecentPaymentHistory(Building $building, int $year): array
    {
        return $this->createQueryBuilder('rc')
            ->select([
                'b.id AS buildingId',
                'rc.id AS regularContributionId',
                'le.id AS ledgerEntryId',
                'le.amount AS paidAmount',
                'le.paymentMethod',
                'le.createdAt AS paymentDate',
                'le.referenceNumber',
                'un.id AS unitId',
                'un.number AS unitNumber',
                'us.id AS ownerId',
                'us.firstName AS ownerFirstName',
                'us.lastName AS ownerLastName',
                'r.id AS receiptId',
                'r.filePath AS receiptFilePath'
            ])
            ->join('rc.building', 'b')
            ->leftJoin('rc.contributionSchedules', 'cs')
            ->leftJoin('cs.unit', 'un')
            ->leftJoin('un.user', 'us')
            ->join(
                'cs.ledgerEntries',
                'le',
                'WITH',
                'le.type = :incomeType AND le.incomeType = :regularContribution'
            )
            ->leftJoin('le.receipt', 'r')
            ->where('b.id = :buildingId')
            ->andWhere('rc.year = :year')
            ->setParameter('buildingId', $building->getId())
            ->setParameter('year', $year)
            ->setParameter('incomeType', 'income')
            ->setParameter('regularContribution', 'regular_contribution')
            ->orderBy('le.createdAt', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
}
