<?php

namespace App\Eventing\Infrastructure;

interface DispatcherInterface
{
    public function dispatch(DomainEvent $event): void;
}