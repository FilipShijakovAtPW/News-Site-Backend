<?php

namespace App\Eventing\Infrastructure;

use Symfony\Contracts\EventDispatcher\Event;

abstract class DomainEvent extends Event
{
    protected \DateTime $createdAt;
    protected $eventName = "";

    public function __construct(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt ?? new \DateTime();
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getName(): string
    {
        return $this->eventName;
    }
}