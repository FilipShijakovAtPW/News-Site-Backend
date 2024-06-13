<?php

namespace App\Model;

use App\Document\User;
use App\Exception\InvalidConfirmationTokenException;
use App\Exception\UserNotFoundException;
use App\Model\Identifier\Identifier;

interface UsersRepositoryInterface
{
    public function getNextIdentifier();

    public function getAllUsers();

    public function getUserById(Identifier $userIdentifier): User;

    public function getUserByConfirmationToken(string $confirmationToken): User;

    public function getUserByUsername(string $username);

    public function getUserConfirmationToken(Identifier $userIdentifier);

    public function saveUser(User $user);

    public function flush();
}