<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
;

class ProgramFixtures extends Fixture
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
        'Arcane' => [
            'synopsis' => 'Championnes de leurs villes jumelles et rivales (la huppée Piltover et la sous-terraine Zaun), deux sœurs Vi et Powder se battent dans une guerre',
            'category' => 'category_Animation',
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        // $program = new Program();
        // $program->setTitle('Walking Dead');
        // $program->setSynopsis('Un virus transforme des gens en zombie');
        // $program->setCategory($this->getReference('category_Horreur'));
        // $manager->persist($program);
        foreach(self::PROGRAM as $titleSerie => $content) {
            $program = new Program();
            $program->setTitle($titleSerie);
            $program->setSynopsis($content['synopsis']);
            $program->setCategory($this->getReference($content['category']));
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