<?php

namespace App\Model;

use App\Entity\User;
use App\Exception\InvalidConfirmationTokenException;
use App\Exception\UserNotFoundException;

interface UsersRepositoryInterface
{
    public function getAllUsers();

    /**
     * @throws UserNotFoundException
     */
    public function getUserById(int $userId): User;

    /**
     * @throws InvalidConfirmationTokenException
     */
    public function getUserByConfirmationToken(string $confirmationToken): User;

    public function saveUser(User $user);

    public function flush();
}