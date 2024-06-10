<?php

namespace App\Commanding\Commands;

use App\Entity\User;

class CreateArticleCommand
{
    private User $user;
    private string $title;
    private string $summary;
    private string $content;

    public function __construct(User $user, string $title, string $summary, string $content)
    {
        $this->user = $user;
        $this->title = $title;
        $this->summary = $summary;
        $this->content = $content;
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