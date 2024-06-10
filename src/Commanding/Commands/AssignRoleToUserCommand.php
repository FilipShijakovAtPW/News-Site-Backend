<?php

namespace App\Commanding\Commands;

class AssignRoleToUserCommand
{
    private int $userId;
    private string $role;

    public function __construct(int $userId, string $role)
    {
        $this->userId = $userId;
        $this->role = $role;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}