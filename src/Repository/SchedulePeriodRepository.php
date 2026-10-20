<?php

namespace App\Repository;

use App\Entity\SchedulePeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchedulePeriod>
 */
class SchedulePeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchedulePeriod::class);
    }

    /**
     * Périodes en cours ou qui commencent dans les $daysAhead prochains jours.
     *
     * @return SchedulePeriod[]
     */
    public function findUpcoming(\DateTimeImmutable $today, int $daysAhead): array
    {
        return $this->createQueryBuilder('period')
            ->andWhere('period.endDate >= :today')
            ->andWhere('period.startDate <= :limit')
            ->setParameter('today', $today->setTime(0, 0))
            ->setParameter('limit', $today->setTime(0, 0)->modify("+{$daysAhead} days"))
            ->orderBy('period.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
