<?php

namespace App\Service;

use App\Entity\Dto\UserAssignRole;
use App\Entity\Dto\UserConfirm;
use App\Entity\User;
use App\Exception\InvalidConfirmationTokenException;
use App\Exception\UserNotFoundException;
use App\Model\UsersRepositoryInterface;
use App\Repository\UserRepository;
use App\Service\Interface\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UsersRepositoryInterface    $usersRepository,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function getAllUsers(): array
    {
        return $this->usersRepository->getAllUsers();
    }

    public function createUser(User $user): string
    {
        $confirmationToken = Uuid::v4()->toBase58();
        $user
            ->setConfirmationToken($confirmationToken)
            ->setIsConfirmed(false);

        $this->usersRepository->saveUser($user);

        return $confirmationToken;
    }

    /**
     * @throws InvalidConfirmationTokenException
     */
    public function confirmUser(string $token, UserConfirm $userConfirm): void
    {
        try {
            $user = $this->usersRepository->getUserByConfirmationToken($token);
        } catch (UserNotFoundException) {
            throw new InvalidConfirmationTokenException();
        }

        $user->confirmUser($this->passwordHasher->hashPassword($user, $userConfirm->getPassword()));

        $this->usersRepository->saveUser($user);
    }

    /**
     * @throws UserNotFoundException
     */
    public function assignRole(UserAssignRole $assignRole): void
    {
        $userId = $assignRole->getUserId();
        $newRole = $assignRole->getRole();

        $user = $this->usersRepository->getUserById($userId);

        $user->assignRole($newRole);

        $this->usersRepository->flush();
    }

    /**
     * @throws UserNotFoundException
     */
    public function removeRole(UserAssignRole $assignRole): void
    {
        $userId = $assignRole->getUserId();
        $newRole = $assignRole->getRole();

        $user = $this->usersRepository->getUserById($userId);

        $user->removeRole($newRole);

        $this->usersRepository->flush();
    }
}