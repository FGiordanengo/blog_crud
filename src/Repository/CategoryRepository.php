<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
    * @return Category[] Returns an array of Category objects
    */
    
    public function listCategories()
    {
        return $this->createQueryBuilder('c')
            ->select('c.title', 'c.slug', 'count(a) as nb')
            ->leftJoin('c.articles', 'a')
            ->andWhere('a.isActive = true')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult()
        ;
    }
    

    
    public function detailedCategory($slug): ?Category
    {
        return $this->createQueryBuilder('c')
            ->addSelect('a')
            ->andWhere('c.slug = :val')
            ->leftJoin('c.articles', 'a')
            ->andWhere('a.isActive = true')
            ->orderBy('a.createdAt', 'DESC')
            ->setParameter('val', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
