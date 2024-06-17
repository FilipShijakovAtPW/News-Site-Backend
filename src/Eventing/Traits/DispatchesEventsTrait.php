<?php

namespace App\Eventing\Traits;

use App\Eventing\Infrastructure\GeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

trait DispatchesEventsTrait
{
    private EventDispatcherInterface $dispatcher;

    public function dispatchEventsFor(GeneratorInterface $eventGenerator) {
        $events = $eventGenerator->release();

        foreach ($events as $event) {
            $this->dispatcher->dispatch($event, $event->getName());
        }
    }

    private function setDispatcher(EventDispatcherInterface $dispatcher) {
        $this->dispatcher = $dispatcher;
    }
}