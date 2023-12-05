<?php

namespace App\Repository;

use App\Entity\DemandePrestataire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandePrestataire>
 *
 * @method DemandePrestataire|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandePrestataire|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandePrestataire[]    findAll()
 * @method DemandePrestataire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandePrestataireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandePrestataire::class);
    }

//    /**
//     * @return DemandePrestataire[] Returns an array of DemandePrestataire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DemandePrestataire
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
