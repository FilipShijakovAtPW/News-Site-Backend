<?php

namespace App\Commanding\Commands;

use App\Model\Identifier\Identifier;

class CreateUserCommand
{
    private Identifier $identifier;
    private string $username;
    private string $email;

    public function __construct(Identifier $identifier, string $username, string $email)
    {
        $this->identifier = $identifier;
        $this->username = $username;
        $this->email = $email;
    }

    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}