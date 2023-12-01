<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\DataFixtures\SeasonFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{   
    const EPISODES = [
        'season1_Walking-Dead' => [
          1 => 'Le Commencement',
          2 => 'Le Decouragement',
          3 => 'Le Final',
        ],
        'season2_Walking-Dead' => [
          1 => 'Le coucou',
          2 => 'Le coco',
          3 => 'Le caca',
        ],
      ];

    public function load(ObjectManager $manager): void
    {

        foreach(self::EPISODES as $season => $arrayEpisodes) {

            foreach($arrayEpisodes as $nbEpisode => $episodeTitle) {
                $episode = new Episode();
                $episode->setSeason($this->getReference($season));
                $episode->setTitle($episodeTitle);
                $episode->setNumber($nbEpisode);
                
                $manager->persist($episode);
            }
        }
            
        // $episode = new Episode();
        // $episode->setTitle(self::EPISODES['season1_Walking-Dead']['title']);
        // $episode->setSeason($this->getReference('season1_Walking-Dead'));
        // $episode->setNumber(self::EPISODES['season1_Walking-Dead']['nbEpisode']);
        // $manager->persist($episode);


        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
        ];
    }
}
