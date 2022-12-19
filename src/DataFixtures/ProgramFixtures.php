<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Program;
use App\DataFixtures\ActorFixtures;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAM_NBR = 5;
    public const PROG_FOR_ACTOR = 3;

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        foreach (CategoryFixtures::CATEGORIES as $category) {
            for ($i = 1; $i <= self::PROGRAM_NBR; $i++) {
                $program = new Program();
                $program->setTitle($faker->sentence(4, true));
                $program->setSynopsis($faker->paragraph(3));
                $program->setCategory($this->getReference('category_' . $category));
                $slug = $this->slugger->slug($program->getTitle());
                $program->setSlug($slug);
                $program->setOwner($this->getReference('user_contributor'));
                $program->setPoster('https://www.ecranlarge.com/media/cache/1600x1200/uploads/image/001/456/9mxcenewbmdxjxdfoijwigoe1tv-987.jpg');
                $this->addReference('program_' . $i . '_' . $category, $program);
                for ($j = 1; $j < self::PROG_FOR_ACTOR; $j++) {
                    $program->addActor($this->getReference('actor_' . $faker->numberBetween(1, 10)));
                }
                $manager->persist($program);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
            ActorFixtures::class,
        ];
    }
}