<?php

namespace App\Model;

use App\Entity\Article;
use App\Entity\User;

interface ArticlesRepositoryInterface
{
    public function getArticleById(int $id);

    public function getPublishedArticlesWithFilters(array $filters);

    public function getAllArticlesWithFilters(array $filters);

    public function getUserArticlesThatMatch(int $userId, array $filters);

    public function saveArticle(Article $article);

    public function flush();


}