<?php

namespace App\DataFixtures\MongoDb;

use App\Document\Article;
use App\Model\Identifier\Identifier;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        foreach(UserFixtures::USERS as $USER)
        {
            for ($i = 0 ; $i < 10 ; $i++) {
                $user = $this->getReference($USER['username']);

                $isPublished = rand(1, 2) == 1;

                $article = Article::getDummy(
                    Identifier::generate()->getId(),
                    $this->faker->realText(30),
                    $this->faker->realText(100),
                    $this->faker->realText(),
                    $this->faker->dateTimeThisYear,
                    $isPublished,
                    $user
                );

                $manager->persist($article);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}