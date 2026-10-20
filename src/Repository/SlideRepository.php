<?php

namespace App\Repository;

use App\Entity\Slide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Slide>
 */
class SlideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slide::class);
    }

    /**
     * @return Slide[]
     */
    public function findOrdered(): array
    {
        return $this->createQueryBuilder('slide')
            ->andWhere('slide.imageName IS NOT NULL')
            ->orderBy('slide.position', 'ASC')
            ->addOrderBy('slide.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
