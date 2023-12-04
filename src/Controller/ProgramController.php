<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Program;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/program', name:'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        return $this->render('program/index.html.twig', ['programs' => $programs]);
    }

    #[Route('/{programID}', methods: ["GET"], requirements:['programID' => '\d+'], name: 'show' )]
    public function show(
        #[MapEntity(mapping: ['programID' => 'id'])] Program $program
    ): Response
    {
        if(!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program->getID() . 'found in program\'s table'
            );
        }

        return $this->render('program/show.html.twig', ['program' => $program]);
    }

    #[Route('/{programID}/season/{seasonID}', methods: ["GET"], requirements:['programID' => '\d+', 'seasonID' => '\d+'], name: 'season_show' )]        
    public function showSeason(
        #[MapEntity(mapping: ['programID' => "id"])] Program $program,
        #[MapEntity(mapping: ['seasonID' => "id"])] Season $season
    ): Response
    {
        return $this->render('program/season_show.html.twig', ['season' => $season, "program" => $program]);
    }

    #[Route('/{programID}/season/{seasonID}/episode/{episodeID}', methods: ["GET"], requirements:['programID' => '\d+', 'seasonID' => '\d+', 'episodeID' => '\d+'], name: 'episode_show' )]
    public function showEpisode(
        #[MapEntity(mapping: ['programID' => "id"])] Program $program,
        #[MapEntity(mapping: ['seasonID' => "id"])] Season $season,
        #[MapEntity(mapping: ['episodeID' => "id"])] Episode $episode
    ): Response
    {
        return $this->render('program/episode_show.html.twig', ['season' => $season, "program" => $program, "episode" => $episode]);
    }
}
