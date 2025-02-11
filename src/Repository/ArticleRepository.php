<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /* useless method swapped by a findBy() in the controller
    public function findAllArticles(bool $includeDisable = false): array
    {
        $query = $this->createQueryBuilder('a');
        if (!$includeDisable) {
            $query->andWhere('a.enable = true');
        }
        return $query
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    */

    public function findLatestArticles(int $limit = 3, bool $includeDisable = false): array
    {
        $query = $this->createQueryBuilder('a'); # 'a' alias of the ArticleRepository

        # Dinguerie
        if (!$includeDisable) {
            $query->andWhere('a.enable = true');
        }

        return $query
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
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

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
