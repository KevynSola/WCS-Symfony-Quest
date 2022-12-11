<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Episode;
use App\DataFixtures\SeasonFixtures;
use App\DataFixtures\ProgramFixtures;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public const EPISODES_NBR = 10;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        foreach (CategoryFixtures::CATEGORIES as $categoryName) {
            for ($i = 1; $i <= ProgramFixtures::PROGRAM_NBR; $i++) {
                for ($j = 1; $j <= SeasonFixtures::SEASON_NBR; $j++) {
                    for ($k = 1; $k <= self::EPISODES_NBR; $k++) {
                        $episode = new Episode();
                        $episode->setTitle($faker->sentence());
                        $episode->setSynopsis($faker->paragraph(4, true));
                        $episode->setNumber($k);
                        $episode->setSeason($this->getReference('program_' . $i . '_' . $categoryName . '_season_' . $j));
                        $manager->persist($episode);
                    }
                }
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