<?php

namespace App\Commanding\Commands;

use App\Model\Identifier\Identifier;

class AssignRoleToUserCommand
{
    private Identifier $userId;
    private string $role;

    public function __construct(Identifier $userId, string $role)
    {
        $this->userId = $userId;
        $this->role = $role;
    }

    public function getUserId(): Identifier
    {
        return $this->userId;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}