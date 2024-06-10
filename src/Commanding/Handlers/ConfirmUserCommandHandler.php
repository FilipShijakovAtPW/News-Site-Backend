<?php

namespace App\Commanding\Handlers;

use App\Commanding\Commands\ConfirmUserCommand;
use App\Model\UsersRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ConfirmUserCommandHandler
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function handle(ConfirmUserCommand $command): void
    {
        $user = $this->usersRepository->getUserByConfirmationToken($command->getToken());

        $user->confirmUser($this->passwordHasher->hashPassword($user, $command->getPassword()));

        $this->usersRepository->flush();
    }
}