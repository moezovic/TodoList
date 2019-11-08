<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Task;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


use Faker;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i=0; $i < 20; $i++) { 
            $task = new Task();
            $task->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true));
            $task->setContent($faker->paragraph($nbSentences = 3, $variableNbSentences = true));
            if ($i < 4) {
                $task->setUser($this->getReference("user_".$i));
            }
            
            $manager->persist($task);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}