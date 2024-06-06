<?php

namespace App\Model;

use App\Entity\User;
use App\Exception\UserNotFoundException;

interface UsersRepositoryInterface
{
    public function getAllUsers();

    /**
     * @throws UserNotFoundException
     */
    public function getUserById(int $userId): User;

    /**
     * @throws UserNotFoundException
     */
    public function getUserByConfirmationToken(string $confirmationToken): User;

    public function saveUser(User $user);

    public function flush();
}