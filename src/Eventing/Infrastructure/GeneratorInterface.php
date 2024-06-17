<?php

namespace App\Eventing\Infrastructure;

interface GeneratorInterface
{
    public function raise(DomainEvent $event): void;

    /**
     * @return DomainEvent[]
     */
    public function release();
}