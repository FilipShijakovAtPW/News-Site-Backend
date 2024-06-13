<?php

namespace App\Commanding\Handlers;

use App\Commanding\Commands\CreateUserCommand;
use App\Document\User;
use App\Model\UsersRepositoryInterface;

class CreateUserCommandHandler
{
    public function __construct(private UsersRepositoryInterface $usersRepository)
    {
    }

    public function handle(CreateUserCommand $command)
    {
        $user = User::create($command->getIdentifier()->getId(), $command->getUsername(), $command->getEmail());

        $this->usersRepository->saveUser($user);
    }
}