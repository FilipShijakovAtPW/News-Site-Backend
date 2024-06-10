<?php

namespace App\Commanding\Handlers;

use App\Commanding\Commands\RemoveRoleFromUserCommand;
use App\Model\UsersRepositoryInterface;

class RemoveRoleFromUserCommandHandler
{
    public function __construct(private UsersRepositoryInterface $usersRepository)
    {
    }

    public function handle(RemoveRoleFromUserCommand $command): void
    {
        $user = $this->usersRepository->getUserById($command->getUserId());

        $user->removeRole($command->getRole());

        $this->usersRepository->flush();
    }
}