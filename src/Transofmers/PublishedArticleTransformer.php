<?php

namespace App\Transofmers;

use App\Document\Article;
use League\Fractal\TransformerAbstract;

class PublishedArticleTransformer extends TransformerAbstract
{

    public function transform(Article $article)
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'summary' => $article->getSummary(),
            'content' => $article->getContent(),
            'published' => $article->getPublished()->format('Y-m-d H:i:s'),
            'user' => [
                'username' => $article->getUser()->getUsername()
            ]
        ];
    }
}