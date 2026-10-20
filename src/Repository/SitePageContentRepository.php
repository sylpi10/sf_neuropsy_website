<?php

namespace App\Repository;

use App\Entity\SitePageContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SitePageContent>
 */
class SitePageContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SitePageContent::class);
    }

    public function findOneWithBlocks(string $routeName): ?SitePageContent
    {
        return $this->createQueryBuilder('content')
            ->leftJoin('content.blocks', 'block')->addSelect('block')
            ->andWhere('content.routeName = :route')
            ->setParameter('route', $routeName)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
