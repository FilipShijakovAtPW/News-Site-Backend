<?php

namespace App\Listeners;

use App\Deserialization\ControllerTraits\WorksWithFractalTrait;
use App\Deserialization\Transformers\ExceptionTransformer;
use App\Exception\BaseException;
use App\Exception\ExceptionTypes\BadRequestExceptionInterface;
use App\Exception\ExceptionTypes\NotFoundExceptionInterface;
use League\Fractal\Resource\Item;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

class ExceptionListener implements EventSubscriberInterface
{
    use WorksWithFractalTrait;

    const EXCEPTION_TO_REQUEST_MAPPER = [
        BadRequestExceptionInterface::class => Response::HTTP_BAD_REQUEST,
        NotFoundExceptionInterface::class => Response::HTTP_NOT_FOUND,
    ];

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'handleException'];
    }

    public function handleException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if (!$throwable instanceof BaseException) {
            return;
        }

        $event->setResponse(
            $this->createJsonResponse(
                new Item($throwable, new ExceptionTransformer()),
                $this->httpStatusCode($throwable)
            )
        );
    }

    private function httpStatusCode(Throwable $throwable): int
    {
        /** @var class-string[] $interfaces */
        $interfaces = class_implements($throwable);

        foreach ($interfaces as $interface) {
            if (array_key_exists($interface, self::EXCEPTION_TO_REQUEST_MAPPER)) {
                return self::EXCEPTION_TO_REQUEST_MAPPER[$interface];
            }
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}