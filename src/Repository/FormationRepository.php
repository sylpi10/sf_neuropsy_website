<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    /**
     * @return Formation[]
     */
    public function findByCategoryCode(string $code): array
    {
        return $this->createQueryBuilder("formation")
            ->innerJoin("formation.category", "category")
            ->addSelect("category")
            ->andWhere("category.code = :code")
            ->setParameter("code", $code)
            ->orderBy("formation.startsAt", "DESC")
            ->addOrderBy("formation.title", "DESC")
            ->getQuery()
            ->getResult();
    }
}
