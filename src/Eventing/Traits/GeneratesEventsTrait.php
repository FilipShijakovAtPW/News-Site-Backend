<?php

namespace App\Eventing\Traits;

use App\Eventing\Infrastructure\DomainEvent;

trait GeneratesEventsTrait
{
    private $events = [];

    public function raise(DomainEvent $event): void
    {
        $this->events[] = $event;
    }

    public function release(): array
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }

}