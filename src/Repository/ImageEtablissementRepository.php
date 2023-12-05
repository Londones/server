<?php

namespace App\Repository;

use App\Entity\ImageEtablissement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImageEtablissement>
 *
 * @method ImageEtablissement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageEtablissement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageEtablissement[]    findAll()
 * @method ImageEtablissement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageEtablissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageEtablissement::class);
    }

//    /**
//     * @return ImageEtablissement[] Returns an array of ImageEtablissement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ImageEtablissement
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
