<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public const EPISODES_NBR = 10;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < self::EPISODES_NBR; $i++) {
            for ($j = 0; $j < SeasonFixtures::SEASON_NBR; $j++) {
                $episode = new Episode();
                $episode->setTitle($faker->sentence(3));
                $episode->setSynopsis($faker->paragraph(4, true));
                $episode->setNumber($faker->numberBetween(1, 10));
                $episode->setSeason($this->getReference('season_' . $faker->numberBetween(0, 10)));
                $manager->persist($episode);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}