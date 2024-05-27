<?php

namespace App\Service;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\Interface\ArticleServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class ArticleService implements ArticleServiceInterface
{
    public function __construct(
        private ArticleRepository $articleRepository
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
        // TODO: Implement getAllArticles() method.
    }

    public function getUserPublishedArticles(int $userId, array $parameters)
    {
        // TODO: Implement getUserPublishedArticles() method.
    }

    public function getUserArticles(int $userId, array $parameters)
    {
        // TODO: Implement getUserArticles() method.
    }
}