<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@news.com',
            'password' => 'Pass123',
            'roles' => [User::ROLE_ADMIN],
        ],
        [
            'username' => 'editor',
            'email' => 'editor@news.com',
            'password' => 'Pass123',
            'roles' => [User::ROLE_EDITOR],
        ],
        [
            'username' => 'writer',
            'email' => 'writer@news.com',
            'password' => 'Pass123',
            'roles' => [User::ROLE_WRITER],
        ],
        [
            'username' => 'reader',
            'email' => 'reader@news.com',
            'password' => 'Pass123',
            'roles' => [],
        ],
    ];

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach (self::USERS as $USER) {
            $user = new User();

            $user->setUsername($USER['username'])
                ->setEmail($USER['email'])
                ->setPassword($this->passwordHasher->hashPassword($user, $USER['password']))
                ->setRoles($USER['roles']);

            $manager->persist($user);
        }
    }
}
