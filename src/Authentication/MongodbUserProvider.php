<?php

namespace App\Authentication;

use App\Document\User;
use App\Model\UsersRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class MongodbUserProvider implements UserProviderInterface
{
    public function __construct(private UsersRepositoryInterface $usersRepository)
    {
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass(string $class)
    {
        return User::class === $class;
    }

    public function loadUserByUsername(string $username)
    {
        return $this->usersRepository->getUserByUsername($username);
    }
}