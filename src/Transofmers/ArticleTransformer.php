<?php

namespace App\Transofmers;

use App\Document\Article;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ArticleTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'isPublished'
    ];

    public function transform(Article $article)
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'summary' => $article->getSummary(),
            'content' => $article->getContent(),
            'published' => $article->getPublished()->format('Y-m-d H:i:s'),
            'user' => [
                'username' => $article->getUser()->getUsername(),
            ],
        ];
    }

    public function includeIsPublished(Article $article)
    {
        return new Item($article->getIsPublished(), function (bool $isPublished) {
            return [
                'isPublished' => $isPublished
            ];
        });
    }
}