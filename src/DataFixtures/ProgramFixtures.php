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
        for ($i = 0; $i < 5; $i++) {
            $program = new Program();
            $program->setTitle('Title ' . $i);
            $program->setSynopsis('La série sur la fille de la famille Adams');
            $program->setCategory($this->getReference('category_Horreur'));
            $program->setPoster('https://fr.web.img4.acsta.net/pictures/22/09/23/15/11/2942764.jpg');

            $manager->persist($program);
        }

        for ($i = 0; $i < 5; $i++) {
            $program = new Program();
            $program->setTitle('Title ' . $i);
            $program->setSynopsis('Il va vraiment falloir remplir tout ça');
            $program->setCategory($this->getReference('category_Science-fiction'));
            $program->setPoster('https://m.media-amazon.com/images/I/81PIj3fMk3L._AC_SL1500_.jpg');

            $manager->persist($program);
        }

        for ($i = 0; $i < 5; $i++) {
            $program = new Program();
            $program->setTitle('Title ' . $i);
            $program->setSynopsis('Il va vraiment falloir remplir tout ça');
            $program->setCategory($this->getReference('category_Action'));
            $program->setPoster('https://fr.web.img6.acsta.net/c_310_420/pictures/22/06/07/11/57/5231272.jpg');

            $manager->persist($program);
        }

        for ($i = 0; $i < 5; $i++) {
            $program = new Program();
            $program->setTitle('Title ' . $i);
            $program->setSynopsis('Il va vraiment falloir remplir tout ça');
            $program->setCategory($this->getReference('category_Animation'));
            $program->setPoster('https://cdn.kulturegeek.fr/wp-content/uploads/2021/07/What-If-Marvel-Affiche.jpg');

            $manager->persist($program);
        }

        for ($i = 0; $i < 5; $i++) {
            $program = new Program();
            $program->setTitle('Title ' . $i);
            $program->setSynopsis('Il va vraiment falloir remplir tout ça');
            $program->setCategory($this->getReference('category_Aventure'));
            $program->setPoster('https://fr.web.img5.acsta.net/c_310_420/pictures/19/12/12/12/13/2421997.jpg');

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