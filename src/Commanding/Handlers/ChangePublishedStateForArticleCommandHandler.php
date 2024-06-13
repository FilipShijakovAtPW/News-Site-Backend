<?php

namespace App\Commanding\Handlers;

use App\Commanding\Commands\ChangePublishedStateForArticleCommand;
use App\Document\Article;
use App\Model\ArticlesRepositoryInterface;

class ChangePublishedStateForArticleCommandHandler
{
    public function __construct(private ArticlesRepositoryInterface $articlesRepository)
    {
    }

    public function handle(ChangePublishedStateForArticleCommand $command): void
    {
        /** @var Article $article */
        $article = $this->articlesRepository->getArticleById($command->getArticleId());

        $article->changeIsPublishedState();

        $this->articlesRepository->flush();
    }
}