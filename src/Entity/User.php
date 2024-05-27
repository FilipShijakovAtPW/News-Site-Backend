<?php

namespace App\Entity;

use App\Deserialization\DenormalizationGroups;
use App\Repository\UserRepository;
use App\Serialization\NormalizationGroups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, Entity
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_EDITOR = 'ROLE_EDITOR';
    public const ROLE_WRITER = 'ROLE_WRITER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([NormalizationGroups::ALL_USERS])]
    private ?int $id = null;

    #[ORM\Column(type: "string")]
    #[Groups([NormalizationGroups::PUBLISHED_ARTICLES, NormalizationGroups::ALL_ARTICLES, NormalizationGroups::ALL_USERS])]
    #[Assert\NotBlank(groups: [DenormalizationGroups::CREATE_USER])]
    #[Assert\Length(min: 7, groups: [DenormalizationGroups::CREATE_USER])]
    private ?string $username;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank(groups: [DenormalizationGroups::CREATE_USER])]
    #[Assert\Email(groups: [DenormalizationGroups::CREATE_USER])]
    #[Groups([NormalizationGroups::ALL_USERS])]
    private ?string $email;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $password;

    #[ORM\Column(type: "boolean", options: ['default' => 0])]
    #[Groups([NormalizationGroups::ALL_USERS])]
    private bool $isConfirmed;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $confirmationToken;

    #[ORM\Column(type: "simple_array", length: 200, nullable: true)]
    #[Groups([NormalizationGroups::ALL_USERS])]
    private array $roles;

    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'user')]
    private Collection $articles;

    public function __construct()
    {
        $this->roles = [];
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): self
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {

    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

}
