<?php

namespace App\DataFixtures;

use App\Entity\Season;
use App\DataFixtures\ProgramFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    const SEASONS = [

        'Walking-Dead' => 12,
        'Star-Wars-:-The-Clone-Wars' => 7,
        'The-Witcher' => 2,
        'No-Game-No-Life'=> 1,
        'Sword-Art-Online' => 4,
        'Arcane' => 1,
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::SEASONS as $program_title => $nbSeason) {
            for($i = 1; $i <= $nbSeason; $i++) {
                $season = new Season();
                $season->setNumber($i);
                $season->setProgram($this->getReference('program_'.$program_title));
                $manager->persist($season);
                $this->addReference('season'. $i .'_'. $program_title, $season);
                
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
