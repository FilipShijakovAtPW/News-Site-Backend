<?php

namespace App\Commanding\Handlers;

use App\Commanding\Commands\AssignRoleToUserCommand;
use App\Model\UsersRepositoryInterface;

class AssignRoleToUserCommandHandler
{
    public function __construct(private UsersRepositoryInterface $usersRepository)
    {
    }

    public function handle(AssignRoleToUserCommand $command): void
    {
        $user = $this->usersRepository->getUserById($command->getUserId());

        $user->assignRole($command->getRole());

        $this->usersRepository->flush();
    }
}