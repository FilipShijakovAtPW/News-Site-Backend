<?php

namespace App\Service\Interface;

interface ArticleServiceInterface
{
    public function getPublishedArticles(array $parameters);

    public function getAllArticles(array $parameters);

    public function getUserPublishedArticles(int $userId, array $parameters);

    public function getUserArticles(int $userId, array $parameters);

}