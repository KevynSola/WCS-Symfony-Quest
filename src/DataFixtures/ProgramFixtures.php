<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Program;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAM_NBR = 5;
    public function load(ObjectManager $manager)
    {
        $j = 1;
        foreach (CategoryFixtures::CATEGORIES as $category) {
            for ($i = 1; $i <= self::PROGRAM_NBR; $i++) {
                $faker = Factory::create();
                $program = new Program();
                $program->setTitle($faker->sentence(4, true));
                $program->setSynopsis($faker->paragraph(3));
                $program->setCategory($this->getReference('category_' . $category));
                $program->setPoster('https://www.ecranlarge.com/media/cache/1600x1200/uploads/image/001/456/9mxcenewbmdxjxdfoijwigoe1tv-987.jpg');
                $this->addReference('program_' . $j, $program);
                $manager->persist($program);
                $j++;
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
        ];
    }
}