<?php

namespace App\Repository;

use App\Entity\OpeningDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OpeningDay>
 */
class OpeningDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpeningDay::class);
    }

    /**
     * @return OpeningDay[]
     */
    public function findOpenDays(): array
    {
        return $this->createQueryBuilder('day')
            ->andWhere('day.open = true')
            ->orderBy('day.dayOfWeek', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
