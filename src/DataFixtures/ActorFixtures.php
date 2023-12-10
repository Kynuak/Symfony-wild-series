<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Actor;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private SluggerInterface $slugger){}
    
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create();

        for ($i = 0; $i <= 10; $i++){
            $actor = new Actor();
            $actorName = $faker->name();
            $actor->setName($actorName);
            $slug = $this->slugger->slug($actorName);
            $actor->setSlug($slug);
            for ($x = 0; $x <= 3; $x++) {
               $actor->addProgram($this->getReference("program_". rand(0, 49))); 
            }
            $manager->persist($actor);
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
