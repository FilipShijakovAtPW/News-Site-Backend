<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@news.com',
            'password' => 'Pass123',
            'isConfirmed' => true,
            'roles' => [User::ROLE_ADMIN],
        ],
        [
            'username' => 'editor',
            'email' => 'editor@news.com',
            'password' => 'Pass123',
            'isConfirmed' => true,
            'roles' => [User::ROLE_EDITOR],
        ],
        [
            'username' => 'writer',
            'email' => 'writer@news.com',
            'password' => 'Pass123',
            'isConfirmed' => true,
            'roles' => [User::ROLE_WRITER],
        ],
        [
            'username' => 'reader',
            'email' => 'reader@news.com',
            'password' => 'Pass123',
            'isConfirmed' => true,
            'roles' => [],
        ],
    ];

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $USER) {
            $user = new User();

            $user->setUsername($USER['username'])
                ->setEmail($USER['email'])
                ->setPassword($this->passwordHasher->hashPassword($user, $USER['password']))
                ->setIsConfirmed($USER['isConfirmed'])
                ->setRoles($USER['roles']);

            $this->addReference($USER['username'], $user);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
