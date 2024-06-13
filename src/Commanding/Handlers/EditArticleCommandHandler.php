<?php

namespace App\Commanding\Handlers;

use App\Commanding\Commands\EditArticleCommand;
use App\Document\Article;
use App\Model\ArticlesRepositoryInterface;

class EditArticleCommandHandler
{
    public function __construct(private ArticlesRepositoryInterface $repository)
    {
    }

    public function handle(EditArticleCommand $command): void
    {
        /** @var Article $article */
        $article = $this->repository->getArticleById($command->getArticleId());

        $article->edit($command->getTitle(), $command->getSummary(), $command->getContent());

        $this->repository->flush();
    }
}