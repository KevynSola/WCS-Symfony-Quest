<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public const SEASON_NBR = 8;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $number = 0;
        for ($j = 0; $j < self::SEASON_NBR; $j++) {
            for ($i = 0; $i < (count(CategoryFixtures::CATEGORIES) * ProgramFixtures::PROGRAM_NBR); $i++) {
                $season = new Season();
                $season->setNumber($faker->numberBetween(1, self::SEASON_NBR));
                $season->setProgram($this->getReference('program_' . $faker->numberBetween(1, count(CategoryFixtures::CATEGORIES) * ProgramFixtures::PROGRAM_NBR)));
                $this->addReference('season_' . $number, $season);
                $manager->persist($season);
                $number++;
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
        ];
    }
}