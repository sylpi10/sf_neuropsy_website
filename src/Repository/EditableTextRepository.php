<?php

namespace App\Repository;

use App\Entity\EditableText;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EditableText>
 */
class EditableTextRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditableText::class);
    }

    /**
     * @return array<string, string|null> contenu indexé par clé
     */
    public function findContentsByRoute(string $routeName): array
    {
        $rows = $this->createQueryBuilder('text')
            ->select('text.key', 'text.content')
            ->andWhere('text.routeName = :route')
            ->setParameter('route', $routeName)
            ->getQuery()
            ->getArrayResult();

        return array_column($rows, 'content', 'key');
    }
}
