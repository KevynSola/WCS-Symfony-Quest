<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Season;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public const SEASON_NBR = 8;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        foreach (CategoryFixtures::CATEGORIES as $categoryName) {

            for ($j = 1; $j <= ProgramFixtures::PROGRAM_NBR; $j++) {
                for ($i = 1; $i <= self::SEASON_NBR; $i++) {
                    $season = new Season();
                    $season->setNumber($i);
                    $season->setProgram($this->getReference('program_' . $j . '_' . $categoryName));
                    $this->addReference('program_' . $j . '_' . $categoryName . '_season_' . $i, $season);

                    $manager->persist($season);
                }
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