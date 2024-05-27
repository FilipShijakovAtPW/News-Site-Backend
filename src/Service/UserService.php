<?php

namespace App\Service;

use App\Entity\Dto\UserAssignRole;
use App\Entity\Dto\UserConfirm;
use App\Entity\User;
use App\Exception\InvalidConfirmationTokenException;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use App\Service\Interface\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserService implements UserServiceInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    public function createUser(User $user): string
    {
        $confirmationToken = Uuid::v4()->toBase58();
        $user
            ->setConfirmationToken($confirmationToken)
            ->setIsConfirmed(false);

        $this->entityManager->persist($user);

        $this->entityManager->flush();

        return $confirmationToken;
    }

    /**
     * @throws InvalidConfirmationTokenException
     */
    public function confirmUser(string $token, UserConfirm $userConfirm): void
    {
        $user =$this->userRepository->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            throw new InvalidConfirmationTokenException();
        }

        $user->setConfirmationToken(null)
            ->setIsConfirmed(true)
            ->setPassword(
                $this->passwordHasher->hashPassword($user, $userConfirm->getPassword())
            );

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @throws UserNotFoundException
     */
    public function assignRole(UserAssignRole $assignRole): void
    {
        $user = $this->userRepository->find($assignRole->getUserId());

        if (!$user) {
            throw new UserNotFoundException($assignRole->getUserId());
        }

        if (in_array($assignRole->getRole(), $user->getRoles())) {
            return;
        }

        $newRoles = $user->getRoles();
        $newRoles[] = $assignRole->getRole();
        $user->setRoles($newRoles);

        $this->entityManager->flush();
    }

    /**
     * @throws UserNotFoundException
     */
    public function removeRole(UserAssignRole $assignRole): void
    {
        $user = $this->userRepository->find($assignRole->getUserId());

        if (!$user) {
            throw new UserNotFoundException($assignRole->getUserId());
        }

        if (!in_array($assignRole->getRole(), $user->getRoles())) {
            return;
        }

        $newRoles = $user->getRoles();
        $key = array_search($assignRole->getRole(), $newRoles);
        unset($newRoles[$key]);

        $user->setRoles($newRoles);

        $this->entityManager->flush();
    }
}