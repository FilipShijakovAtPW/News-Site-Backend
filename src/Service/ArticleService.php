<?php

namespace App\Service;

use App\Repository\ArticleRepository;
use App\Service\Interface\ArticleServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class ArticleService implements ArticleServiceInterface
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function getPublishedArticles(array $parameters): array
    {
        return $this->articleRepository->findAllThatMatch($parameters)
            ->andWhere('a.isPublished = 1')
            ->getQuery()
            ->getResult();
    }

    public function getAllArticles(array $parameters)
    {
        return $this->articleRepository->findAllThatMatch($parameters)
            ->getQuery()
            ->getResult();
    }

    public function getUserPublishedArticles(int $userId, array $parameters)
    {
        // TODO: Implement getUserPublishedArticles() method.
    }

    public function getUserArticles(int $userId, array $parameters)
    {
        // TODO: Implement getUserArticles() method.
    }

    public function changePublishedStateForArticle(int $articleId): void
    {
       $article = $this->articleRepository->find($articleId);

       $article->changeIsPublishedState();

       $this->entityManager->flush();
    }
}