<?php

namespace App\Commanding\Handlers;

use App\Commanding\Commands\CreateArticleCommand;
use App\Document\Article;
use App\Model\ArticlesRepositoryInterface;

class CreateArticleCommandHandler
{
    public function __construct(private ArticlesRepositoryInterface $articlesRepository)
    {
    }

    public function handle(CreateArticleCommand $command): void
    {
        $article = Article::create(
            $command->getIdentifier()->getId(),
            $command->getUser(),
            $command->getTitle(),
            $command->getSummary(),
            $command->getContent()
        );

        $this->articlesRepository->saveArticle($article);
    }
}