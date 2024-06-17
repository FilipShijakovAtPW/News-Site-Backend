<?php

namespace App\Eventing\Events;

use App\Eventing\Infrastructure\DomainEvent;

class UserCreatedEvent extends DomainEvent
{
    public $eventName = 'user.created';
    public const NAME = 'user.created';
    private string $email;
    private string $userConfirmationToken;

    public function __construct(string $email, string $userConfirmationToken)
    {
        parent::__construct();
        $this->email = $email;
        $this->userConfirmationToken = $userConfirmationToken;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUserConfirmationToken(): string
    {
        return $this->userConfirmationToken;
    }

}