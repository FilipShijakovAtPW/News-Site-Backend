<?php

namespace App\Document;

use App\Model\Identifier\Identifier;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Types\Type;

#[ODM\Document(collection: 'articles')]
class Article
{
    #[ODM\Id(strategy: 'NONE')]
    private ?string $id = null;

    #[ODM\Field(type: Type::STRING)]
    private ?string $title = null;

    #[ODM\Field(type: Type::STRING)]
    private ?string $summary = null;

    #[ODM\Field(type: Type::STRING)]
    private ?string $content = null;

    #[ODM\Field(type: Type::DATE)]
    private ?\DateTimeInterface $published = null;

    #[ODM\Field(type: Type::BOOL, options: ['default' => false])]
    private ?bool $isPublished = null;

    #[ODM\ReferenceOne(storeAs: 'id', targetDocument: User::class, inversedBy: 'articles')]
    private ?User $user = null;

    public static function create(string $id, User $user, string $title, string $summary, string $content): self
    {
        $article = new Article();

        $article->id = $id;
        $article->user = $user;
        $article->title = $title;
        $article->summary = $summary;
        $article->content = $content;
        $article->published = new \DateTime();
        $article->isPublished = false;

        return $article;
    }

    public static function getDummy(
        string              $id,
        ?string             $title,
        ?string             $summary,
        ?string             $content,
        ?\DateTimeInterface $published,
        ?bool               $isPublished,
        ?User               $user
    ): Article
    {
        $article = new Article();

        $article->id = $id;
        $article->title = $title;
        $article->summary = $summary;
        $article->content = $content;
        $article->published = $published;
        $article->isPublished = $isPublished;
        $article->user = $user;

        return $article;
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function edit(?string $title, ?string $summary, ?string $content): void
    {
        $this->title = $title ?? $this->title;
        $this->summary = $summary ?? $this->summary;
        $this->content = $content ?? $this->content;
    }

    public function changeIsPublishedState(): void
    {
        $this->isPublished = !$this->isPublished;
    }

}