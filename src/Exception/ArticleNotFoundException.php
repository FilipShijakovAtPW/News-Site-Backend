<?php

namespace App\Exception;

class ArticleNotFoundException extends \Exception
{
    public function __construct(int $articleId)
    {
        parent::__construct("Article with id $articleId not found");
    }
}