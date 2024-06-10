<?php

namespace App\Commanding\Commands;

class ChangePublishedStateForArticleCommand
{
    private int $articleId;

    public function __construct(int $articleId)
    {
        $this->articleId = $articleId;
    }

    /**
     * @return int
     */
    public function getArticleId(): int
    {
        return $this->articleId;
    }
}