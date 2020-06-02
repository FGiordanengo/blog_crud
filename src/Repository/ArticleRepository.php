<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

     /**
      * @return Article[] Returns an array of Article objects
      */
    
    public function findAllPublished()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('c')
            ->andWhere('a.isActive = true')
            ->leftjoin('a.category', 'c')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function detailedArticle($slug): ?Article
    {
        return $this->createQueryBuilder('a')
            ->addSelect('c')
            ->leftJoin('a.category', 'c')
            ->andWhere('a.slug = :slug')
            ->andWhere('a.isActive = true')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
