<?php

namespace App\Model\RelationalDB;

use App\Entity\Article;
use App\Exception\ArticleNotFoundException;
use App\Model\ArticlesRepositoryInterface;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class RelationalArticlesRepository implements ArticlesRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    private function getRepository(): ArticleRepository|EntityRepository
    {
        return $this->entityManager->getRepository(Article::class);
    }

    private function getQueryBuilderWithFilters($filters): QueryBuilder
    {
        $pageSize = $filters['pageSize'];
        $pageStart = ($filters['pageNumber'] - 1) * $pageSize;

        $qb = $this->getRepository()->createQueryBuilder("a");


        if ($filters['matches'])
        {
            $qb->andWhere("a.title = :title")
                ->setParameter('title', "{$filters['matches']}");

        }
        else if ($filters['contains'])
        {
            $qb->andWhere("a.title LIKE :title")
                ->setParameter('title', "%{$filters['contains']}%");
        }

        if ($filters['orderByAsc'])
        {
            $qb->orderBy("a.{$filters['orderByAsc']}", 'ASC');
        }
        else if ($filters['orderByDesc'])
        {
            $qb->orderBy("a.{$filters['orderByDesc']}", 'DESC');
        }

        return $qb->setFirstResult($pageStart)
            ->setMaxResults($pageSize);
    }

    public function getArticleById(int $id): Article
    {
        $article = $this->getRepository()->find($id);
        if ($article === null) {
            throw new ArticleNotFoundException($id);
        }
        return $article;
    }

    public function getPublishedArticlesWithFilters(array $filters)
    {
        return $this->getQueryBuilderWithFilters($filters)
            ->andWhere('a.isPublished = 1')
            ->getQuery()
            ->getResult();
    }

    public function getAllArticlesWithFilters(array $filters)
    {
        return $this->getQueryBuilderWithFilters($filters)
            ->getQuery()
            ->getResult();
    }

    public function getUserArticlesWithFilters(int $userId, array $filters)
    {
        return $this->getQueryBuilderWithFilters($filters)
            ->andWhere('a.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function saveArticle(Article $article): void
    {
        $this->entityManager->persist($article);
        $this->entityManager->flush();
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}