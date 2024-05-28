<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\User;
use App\Exception\ArticleNotFoundException;
use App\Exception\UserCantEditOthersArticleException;
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

    public function getUserArticles(User $user, array $parameters)
    {
        return $this->articleRepository->findAllThatMatch($parameters)
            ->andWhere('a.user = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }

    public function changePublishedStateForArticle(int $articleId): void
    {
       $article = $this->articleRepository->find($articleId);

       $article->changeIsPublishedState();

       $this->entityManager->flush();
    }

    public function createArticle(Article $article): Article
    {
        $article
            ->setPublished(new \DateTime())
            ->setIsPublished(false);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $article;
    }

    /**
     * @throws ArticleNotFoundException
     * @throws UserCantEditOthersArticleException
     */
    public function editArticle(int $articleId, User $user, Article $article): Article
    {
        $articleFromDb = $this->articleRepository->find($articleId);

        if (!$articleFromDb) {
            throw new ArticleNotFoundException($articleId);
        }

        if ($articleFromDb->getUser()->getId() !== $user->getId()) {
            throw new UserCantEditOthersArticleException();
        }

        if ($article->getTitle() && strlen($article->getTitle()) !== 0) {
            $articleFromDb->setTitle($article->getTitle());
        }

        if ($article->getContent() && strlen($article->getContent()) !== 0) {
            $articleFromDb->setContent($article->getContent());
        }

        if ($article->getSummary() && strlen($article->getSummary()) !== 0) {
            $articleFromDb->setSummary($article->getSummary());
        }

        $this->entityManager->flush();

        return $articleFromDb;
    }

}