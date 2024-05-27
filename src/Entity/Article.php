<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use App\Serialization\NormalizationGroups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([NormalizationGroups::PUBLISHED_ARTICLES])]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([NormalizationGroups::PUBLISHED_ARTICLES])]
    private ?string $title;

    #[ORM\Column(type: 'string')]
    #[Groups([NormalizationGroups::PUBLISHED_ARTICLES])]
    private ?string $summary;

    #[ORM\Column(type: 'text')]
    #[Groups([NormalizationGroups::PUBLISHED_ARTICLES])]
    private ?string $content;

    #[ORM\Column(type: 'datetime')]
    #[Groups([NormalizationGroups::PUBLISHED_ARTICLES])]
    private \DateTimeInterface $published;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isPublished;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([NormalizationGroups::PUBLISHED_ARTICLES])]
    private User $user;

    public function getId(): ?int
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

    public function getPublished(): \DateTimeInterface
    {
        return $this->published;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}
