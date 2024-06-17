<?php

namespace App\Commanding\Handlers;

use App\Commanding\Commands\CreateUserCommand;
use App\Document\User;
use App\Eventing\Traits\DispatchesEventsTrait;
use App\Model\UsersRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateUserCommandHandler
{
    use DispatchesEventsTrait;

    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    public function handle(CreateUserCommand $command)
    {
        $user = User::create($command->getIdentifier()->getId(), $command->getUsername(), $command->getEmail());

        $this->dispatchEventsFor($user);

        $this->usersRepository->saveUser($user);
    }
}