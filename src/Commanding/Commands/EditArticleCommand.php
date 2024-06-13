<?php

namespace App\Commanding\Commands;

use App\Model\Identifier\Identifier;

class EditArticleCommand
{
    private Identifier $articleId;
    private ?string $title;
    private ?string $summary;
    private ?string $content;

    public function __construct(Identifier $articleId, ?string $title, ?string $summary, ?string $content)
    {
        $this->articleId = $articleId;
        $this->title = $title;
        $this->summary = $summary;
        $this->content = $content;
    }

    public function getArticleId(): Identifier
    {
        return $this->articleId;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
}