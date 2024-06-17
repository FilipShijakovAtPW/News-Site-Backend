<?php

namespace App\Document;

use App\Eventing\Events\UserCreatedEvent;
use App\Eventing\Infrastructure\GeneratorInterface;
use App\Eventing\Traits\GeneratesEventsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Types\Type;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ODM\Document(collection: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, GeneratorInterface
{
    use GeneratesEventsTrait;

    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_EDITOR = 'ROLE_EDITOR';
    public const ROLE_WRITER = 'ROLE_WRITER';

    #[ODM\Id(strategy: 'NONE')]
    private ?string $id = null;

    #[ODM\Field(type: Type::STRING)]
    private ?string $username;

    #[ODM\Field(type: Type::STRING)]
    private ?string $email;

    #[ODM\Field(type: Type::STRING, nullable: true)]
    private ?string $password;

    #[ODM\Field(type: Type::BOOL, options: ['default' => 0])]
    private bool $isConfirmed;

    #[ODM\Field(type: Type::STRING, nullable: true)]
    private ?string $confirmationToken;

    #[ODM\Field(type: Type::COLLECTION, nullable: true)]
    private ?array $roles;

    #[ODM\ReferenceMany(targetDocument: Article::class, mappedBy: 'user')]
    private Collection $articles;

    private function __construct(string $id, string $username, string $email)
    {
        $this->articles = new ArrayCollection();
        $this->roles = [];

        $this->id = $id;
        $this->username = $username;
        $this->email = $email;

        $confirmationToken = Uuid::v4()->toBase58();
        $this->confirmationToken = $confirmationToken;
        $this->isConfirmed = false;

        $this->raise(new UserCreatedEvent($email, $confirmationToken));
    }

    public static function create(string $id, string $username, string $email): self
    {
        return new User($id, $username, $email);
    }

    public static function getDummy(
        string                      $id,
        ?string                     $username,
        ?string                     $email,
        ?string                     $password,
        bool                        $isConfirmed,
        array                       $roles,
        UserPasswordHasherInterface $passwordHasher
    ): self
    {
        $user = new User($id, $username, $email);

        $user->password = $password != null ? $passwordHasher->hashPassword($user, $password) : null;
        $user->isConfirmed = $isConfirmed;
        $user->roles = $roles;

        if (!$isConfirmed) {
            $user->confirmationToken = null;
        }

        return $user;
    }


    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->password;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function assignRole(string $role): void
    {
        if (in_array($role, $this->getRoles())) {
            return;
        }

        $this->roles[] = $role;
    }

    public function removeRole(string $role): void
    {
        $userRoles = $this->getRoles();
        if (!in_array($role, $userRoles)) {
            return;
        }

        $this->roles = array_filter($userRoles, function ($userRole) use ($role) {
            return $userRole !== $role;
        });
    }

    public function confirmUser(string $password): void
    {
        $this->confirmationToken = null;
        $this->isConfirmed = true;
        $this->password = $password;
    }
}