<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function show(string $programID, ProgramRepository $programRepository): Response
    {

        $program = $programRepository->findOneBy(['id' => $programID]);

        if(!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $programID . 'found in program\'s table'
            );
        }

        return $this->render('program/show.html.twig', ['program' => $program]);
    }

    #[Route('/{programID}/season/{seasonID}', methods: ["GET"], requirements:['programID' => '\d+', 'seasonID' => '\d+'], name: 'season_show' )]
    public function showSeason(int $programID, int $seasonID, ProgramRepository $programRepository, SeasonRepository $seasonRepository)
    {
        $program = $programRepository->find($programID);
        $season = $seasonRepository->find($seasonID);
        $episodes = $season->getEpisodes();

        return $this->render('program/season_show.html.twig', ['season' => $season, "program" => $program, "episodes" => $episodes]);
    }
}
