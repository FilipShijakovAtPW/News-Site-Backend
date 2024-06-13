<?php

namespace App\Commanding\Commands;

use App\Document\User;
use App\Model\Identifier\Identifier;

class CreateArticleCommand
{
    private Identifier $identifier;
    private User $user;
    private string $title;
    private string $summary;
    private string $content;

    public function __construct(Identifier $identifier, User $user, string $title, string $summary, string $content)
    {
        $this->identifier = $identifier;
        $this->user = $user;
        $this->title = $title;
        $this->summary = $summary;
        $this->content = $content;
    }

    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}