<?php

namespace App\Service\Interface;

use App\Entity\Article;
use App\Entity\User;
use App\Exception\ArticleNotFoundException;
use App\Exception\UserCantEditOthersArticleException;

interface ArticleServiceInterface
{
    public function getPublishedArticles(array $parameters);

    public function getAllArticles(array $parameters);

    public function getUserArticles(User $user, array $parameters);

    public function changePublishedStateForArticle(int $articleId);

    public function createArticle(Article $article): Article;

    /**
     * @throws ArticleNotFoundException
     * @throws UserCantEditOthersArticleException
     */
    public function editArticle(int $articleId, User $user, Article $article): Article;

}