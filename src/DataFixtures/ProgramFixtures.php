<?php

namespace App\DataFixtures;

use App\Entity\Program;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    const PROGRAM = [
        'Walking Dead' => [
            "synopsis" => 'Un virus transforme les gens en zombie',
            "category" => 'category_Horreur',
        ],
        'Star Wars : The Clone Wars' => [
            'synopsis' => 'La guerre des entre la République et les Séparatistes',
            'category' => 'category_Science-fiction',
        ],
        'The Witcher' => [
            'synopsis' => 'Le sorceleur Geralt, un chasseur de monstres mutant.',
            'category' => 'category_Action',
        ],
        'No Game No Life' => [
            'synopsis' => 'Un frère et une soeur se retrouve dans un monde parallèle où la lois c\'est les jeux.',
            'category' => 'category_Fantasy',
        ],
        'Sword Art Online' => [
            'synopsis' => 'Kirito, un gamer se retrouver coincé dans le nouveau jeu vidéo Sword Art Online, s\il meurt dans le jeu, il meurt dans la vie réelle.',
            'category' => 'category_Fantasy',
        ],
        'Arcane' => [
            'synopsis' => 'Championnes de leurs villes jumelles et rivales (la huppée Piltover et la sous-terraine Zaun), deux sœurs Vi et Powder se battent dans une guerre',
            'category' => 'category_Animation',
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::PROGRAM as $titleSerie => $content) {
            $program = new Program();
            $program->setTitle($titleSerie);
            $program->setSynopsis($content['synopsis']);
            $program->setCategory($this->getReference($content['category']));
            $titleSerie = str_replace(" ", "-", $titleSerie);
            $this->addReference('program_' . $titleSerie, $program);
            $manager->persist($program);
           
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
