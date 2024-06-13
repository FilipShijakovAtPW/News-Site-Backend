<?php

namespace App\Commanding\Commands;

use App\Model\Identifier\Identifier;

class ChangePublishedStateForArticleCommand
{
    private Identifier $articleId;

    public function __construct(Identifier $articleId)
    {
        $this->articleId = $articleId;
    }

    /**
     * @return int
     */
    public function getArticleId(): Identifier
    {
        return $this->articleId;
    }
}