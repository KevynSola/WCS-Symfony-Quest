<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ActorFixtures extends Fixture
{
    public const NBR_ACTOR = 10;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i=1; $i <= self::NBR_ACTOR; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name());
            $this->addReference('actor_' . $i, $actor);
            $manager->persist($actor);
        }

        $manager->flush();
    }
}
