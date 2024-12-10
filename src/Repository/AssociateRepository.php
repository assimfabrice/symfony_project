<?php

namespace App\Repository;

use App\Entity\Associate;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Associate>
 */
class AssociateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Associate::class);
    }
    public function findAssociatesSocietesByUser(User $user)
    {
        $id = $user->getId();
        return $this->createQueryBuilder('a')
            ->addSelect('a')
            ->join('a.societe', 's')
            ->andWhere('s.user = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
    public function findAssociateBySociete(int $societeId = 0)
    {
        return $this->createQueryBuilder('a')
            ->join('a.societe', 's')
            ->andWhere('s.id = :id')
            ->setParameter('id', $societeId)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Associe[] Returns an array of Associe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Associe
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
