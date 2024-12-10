<?php

namespace App\Repository;

use App\Entity\Societe;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Societe>
 */
class SocieteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Societe::class);
    }
    public function findAssociatesByUser(
        User $user,
        int $idSociete
    )
    {
        $id = $user->getId();

        return $this->createQueryBuilder('s')
                ->addSelect('a')
                ->join('s.user', 'user')
                ->innerJoin('s.associates', 'a')
                ->andWhere('s.user = :id')
                ->andWhere('a.societe = :idSociete')
                ->setParameter('id', $id)
                ->setParameter('idSociete', $idSociete)
                ->getQuery()
                ->getSingleResult();
    }
    /**
     * @param User $user
     * @param int $companyType
     * @return mixed
     */
    public function findMaxSociete(
        User $user,
        int $companyType,
    ): mixed
    {
        $id = $user->getId();

        return $this->createQueryBuilder('s')
            ->select('MAX(s.id) as maxSocieteId')
            ->andWhere('s.user = :id')
            ->andWhere('s.companyType = :companyType')
            ->setParameter('id', $id)
            ->setParameter('companyType', $companyType)
            ->getQuery()
            ->getSingleResult();
    }
    public function findByUserByCompany(
        int $userId,
        int $societeId,
        int $companyType,
    ): array|string
    {
        return $this->createQueryBuilder('s')
            ->addSelect('a')
            ->join('s.user', 'user')
            ->join('s.associates', 'a')
            ->andWhere('s.user = :user_id')
            ->andWhere('s.id = :id')
            ->andWhere('s.companyType = :companyType')
            ->setParameter('user_id', $userId)
            ->setParameter('id', $societeId)
            ->setParameter('companyType', $companyType)
            ->getQuery()
            ->getResult();
    }

    public function findSocieteNotNull()
    {
        $query = $this->createQueryBuilder('s');
        return $query
            ->join('s.associates', 'a')
            ->andWhere($query->expr()->isNotNull('s.user'))
            ->andWhere($query->expr()->isNotNull('a.societe'))
            ->getQuery()
            ->getResult();
    }
}
