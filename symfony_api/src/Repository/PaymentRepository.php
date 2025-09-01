<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function findActiveByUnit(int $unitId): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.unit', 'u')
            ->andWhere('u.id = :unitId')
            ->setParameter('unitId', $unitId)
            ->orderBy('p.paymentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
