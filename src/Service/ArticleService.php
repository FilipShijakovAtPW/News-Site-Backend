<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\User;
use App\Exception\ArticleNotFoundException;
use App\Exception\UserCantEditOthersArticleException;
use App\Model\ArticlesRepositoryInterface;
use App\Repository\ArticleRepository;
use App\Service\Interface\ArticleServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class ArticleService implements ArticleServiceInterface
{
    public function __construct(
        private ArticlesRepositoryInterface $articlesRepository
    )
    {
    }

    public function getPublishedArticles(array $parameters): array
    {
        return $this->articlesRepository->getPublishedArticlesWithFilters($parameters);
    }

    public function getAllArticles(array $parameters)
    {
        return $this->articlesRepository->getAllArticlesWithFilters($parameters);
    }

    public function getUserArticles(User $user, array $parameters)
    {
        return $this->articlesRepository->getUserArticlesWithFilters($user->getId(), $parameters);
    }

    public function changePublishedStateForArticle(int $articleId): void
    {
       $article = $this->articlesRepository->getArticleById($articleId);

       $article->changeIsPublishedState();

       $this->articlesRepository->flush();
    }

    public function createArticle(Article $article): Article
    {
        $article
            ->setPublished(new \DateTime())
            ->setIsPublished(false);

        $this->articlesRepository->saveArticle($article);

        return $article;
    }

    /**
     * @throws ArticleNotFoundException
     */
    public function editArticle(int $articleId, Article $article): Article
    {
        $articleFromDb = $this->articlesRepository->getArticleById($articleId);

        if ($article->getTitle() && strlen($article->getTitle()) !== 0) {
            $articleFromDb->setTitle($article->getTitle());
        }

        if ($article->getContent() && strlen($article->getContent()) !== 0) {
            $articleFromDb->setContent($article->getContent());
        }

        if ($article->getSummary() && strlen($article->getSummary()) !== 0) {
            $articleFromDb->setSummary($article->getSummary());
        }

        $this->articlesRepository->flush();

        return $articleFromDb;
    }

}