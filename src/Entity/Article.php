<?php

namespace App\Entity;

use App\Deserialization\DenormalizationGroups;
use App\Repository\ArticleRepository;
use App\Serialization\NormalizationGroups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article implements Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([NormalizationGroups::ALL_ARTICLES])]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([NormalizationGroups::ALL_ARTICLES])]
    #[Assert\NotBlank(groups: [DenormalizationGroups::CREATE_ARTICLE])]
    private ?string $title = null;

    #[ORM\Column(type: 'string')]
    #[Groups([NormalizationGroups::ALL_ARTICLES])]
    #[Assert\NotBlank(groups: [DenormalizationGroups::CREATE_ARTICLE])]
    private ?string $summary = null;

    #[ORM\Column(type: 'text')]
    #[Groups([NormalizationGroups::ALL_ARTICLES])]
    #[Assert\NotBlank(groups: [DenormalizationGroups::CREATE_ARTICLE])]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups([NormalizationGroups::ALL_ARTICLES])]
    private ?\DateTimeInterface $published = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups([NormalizationGroups::ALL_ARTICLES])]
    private ?bool $isPublished = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([NormalizationGroups::ALL_ARTICLES])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $dateTime): self
    {
        $this->published = $dateTime;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function changeIsPublishedState(): void
    {
        $this->isPublished = !$this->isPublished;
    }

}
