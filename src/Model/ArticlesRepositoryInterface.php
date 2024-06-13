<?php

namespace App\Model;

use App\Document\Article;
use App\Document\User;
use App\Model\Identifier\Identifier;

interface ArticlesRepositoryInterface
{
    public function getNextIdentifier();
    public function getArticleById(Identifier $identifier);

    public function getPublishedArticlesWithFilters(array $filters);

    public function getAllArticlesWithFilters(array $filters);

    public function getUserArticlesWithFilters(User $user, array $filters);

    public function saveArticle(Article $article);

    public function flush();


}