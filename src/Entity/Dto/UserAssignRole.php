<?php

namespace App\Entity\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserAssignRole
{
    #[Assert\NotBlank]
    private ?int $userId;

    #[Assert\NotBlank]
    private ?string $role;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): void
    {
        $this->role = $role;
    }


}