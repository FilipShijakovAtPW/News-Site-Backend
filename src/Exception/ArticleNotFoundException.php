<?php

namespace App\Exception;

use App\Exception\ExceptionTypes\NotFoundExceptionInterface;

class ArticleNotFoundException extends BaseException implements NotFoundExceptionInterface
{
    public function __construct(int $articleId)
    {
        parent::__construct("Article with id $articleId not found");
    }

    public function getErrors()
    {
        return $this->getMessage();
    }
}