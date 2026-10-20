<?php

namespace App\Repository;

use App\Entity\Fee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fee>
 */
class FeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fee::class);
    }

    /**
     * @return Fee[]
     */
    public function findOrdered(): array
    {
        return $this->createQueryBuilder('fee')
            ->orderBy('fee.position', 'ASC')
            ->addOrderBy('fee.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
