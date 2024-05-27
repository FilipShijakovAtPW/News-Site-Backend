<?php

namespace App\Service\Interface;

use App\Entity\Dto\UserAssignRole;
use App\Entity\Dto\UserConfirm;
use App\Entity\User;
use App\Exception\InvalidConfirmationTokenException;
use App\Exception\UserNotFoundException;

interface UserServiceInterface
{
    public function getAllUsers(): array;

    public function createUser(User $user): string;

    /**
     * @throws InvalidConfirmationTokenException
     */
    public function confirmUser(string $token, UserConfirm $userConfirm): void;

    /**
     * @throws UserNotFoundException
     */
    public function assignRole(UserAssignRole $assignRole): void;

    /**
     * @throws UserNotFoundException
     */
    public function removeRole(UserAssignRole $assignRole): void;
}