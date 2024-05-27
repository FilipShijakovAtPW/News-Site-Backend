<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
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

    public function findAllThatMatch(array $parameters) {
        $pageSize = $parameters['pageSize'];
        $pageStart = ($parameters['pageNumber'] - 1) * $pageSize;

        $qb = $this->createQueryBuilder('a');

        if ($parameters['matches'])
        {
            $qb->andWhere("a.title = :title")
            ->setParameter('title', "{$parameters['matches']}");

        }
        else if ($parameters['contains'])
        {
            $qb->andWhere("a.title LIKE :title")
            ->setParameter('title', "%{$parameters['contains']}%");
        }

        if ($parameters['orderByAsc'])
        {
            $qb->orderBy("a.{$parameters['orderByAsc']}", 'ASC');
        }
        else if ($parameters['orderByDesc'])
        {
            $qb->orderBy("a.{$parameters['orderByDesc']}", 'DESC');
        }

        return $qb->setFirstResult($pageStart)
            ->setMaxResults($pageSize);
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
