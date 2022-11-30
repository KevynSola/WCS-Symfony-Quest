<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $program = new Program();
            $program->setTitle('Title ' . $i);
            $program->setSynopsis('La série sur la fille de la famille Adams');
            $program->setCategory($this->getReference('category_Action'));
            $program->setPoster('https://fr.web.img4.acsta.net/pictures/22/09/23/15/11/2942764.jpg');

            $manager->persist($program);
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