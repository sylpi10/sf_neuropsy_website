<?php

namespace App\Repository;

use App\Entity\MenuItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MenuItem>
 */
class MenuItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuItem::class);
    }

    /**
     * Éléments principaux visibles, avec leurs sous-éléments chargés (une seule requête).
     *
     * @return MenuItem[]
     */
    public function findMenu(): array
    {
        return $this->createQueryBuilder('item')
            ->leftJoin('item.children', 'child')->addSelect('child')
            ->leftJoin('item.page', 'page')->addSelect('page')
            ->leftJoin('child.page', 'childPage')->addSelect('childPage')
            ->andWhere('item.parent IS NULL')
            ->andWhere('item.visible = true')
            ->orderBy('item.position', 'ASC')
            ->addOrderBy('item.id', 'ASC')
            ->addOrderBy('child.position', 'ASC')
            ->addOrderBy('child.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
