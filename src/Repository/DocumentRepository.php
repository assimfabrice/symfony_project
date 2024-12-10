<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\DocumentSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;

class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly PaginatorInterface $paginator)
    {
        parent::__construct($registry, Document::class);
    }
    public function findSearch(DocumentSearch $search): \Knp\Component\Pager\Pagination\PaginationInterface
    {
        $query = $this->createQueryBuilder('d')
            ->select('ct', 'd')
            ->join('d.companyType', 'ct');

        if(!empty($search->getTitle())) {
            $query = $query
                ->andWhere('d.title LIKE :title')
                ->setParameter('title', "%{$search->getTitle()}%");
        }

        if(!empty($search->getCompanyType())) {
            $query = $query
                ->andWhere('ct.id IN (:companyType)')
                ->setParameter('companyType', $search->getCompanyType());
        }

        $query = $query->getQuery();

        return $this->paginator->paginate(
            $query,
            $search->page,
            3
        );
        //return $query->getQuery()->getResult();
    }
}