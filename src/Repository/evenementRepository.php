<?php

namespace App\Repository;

use App\Entity\evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 */
class evenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    /**
     * Recherche des événements par un terme donné dans le nom, lieu, description ou nom de catégorie.
     *
     * @param string $term Le terme de recherche
     * @return Evenement[] Résultat de la recherche
     */
    public function searchEvents(string $searchQuery)
    {
        return $this->createQueryBuilder('e')
            ->where('e.nom LIKE :search OR e.lieu LIKE :search OR e.categories LIKE :search')
            ->setParameter('search', '%' . $searchQuery . '%')
            ->getQuery()
            ->getResult();
    }
}
