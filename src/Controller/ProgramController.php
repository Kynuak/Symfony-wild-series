<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Repository\CommentRepository;
use App\Service\ProgramDuration;
use Symfony\Component\Mime\Email;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/program', name:'program_')]
class ProgramController extends AbstractController
{
    

    public function __construct(private Security $security){}

    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        if(!$session->has('total')) {
            $session->set('total', 0);
        }

        $total = $session->get('total');

        $programs = $programRepository->findAll();
        return $this->render('program/index.html.twig', ['programs' => $programs]);
    }


    #[Route('/new', name: 'new')]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManagerInterface,
        SluggerInterface $slugger,
        MailerInterface $mailer,
    ): Response
    {

        $program = new Program();
        $test = [];
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $entityManagerInterface->persist($program);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'The new program has been created');

            $email = (new Email())
                    ->from($this->getParameter('mailer_from'))
                    ->to('aee53c68ef-307150@inbox.mailtrap.io')
                    ->subject('Une nouvelle série à été publié !')
                    ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));
            $mailer->send($email);



            return $this->redirectToRoute('program_index');
        }


        return $this->render('program/new.html.twig', ['form' => $form, "test" => $test]);
    }

    #[Route('/admin', name: 'admin')]
    public function adminProgram(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        return $this->render('program/admin_index.html.twig', ['programs' => $programs]);
    }

    #[Route('/admin/{slug}/edit', name: 'admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Program $program,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
    ): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $entityManager->flush();

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'admin_delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();
        }

        $this->addFlash('warning', 'The episode has been deleted');
        return $this->redirectToRoute('program_admin', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{programSlug}', methods: ["GET"], name: 'show' )]
    public function show(
        #[MapEntity(mapping: ['programSlug' => 'slug'])] Program $program,
        ProgramDuration $programDuration
    ): Response
    {
        if(!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program->getSlug() . 'found in program\'s table'
            );
        }

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'programDuration' => $programDuration->calculate($program)
        ]);
    }

    #[Route('/{programSlug}/season/{seasonID}', methods: ["GET"], requirements:['seasonID' => '\d+'], name: 'season_show' )]        
    public function showSeason(
        #[MapEntity(mapping: ['programSlug' => "slug"])] Program $program,
        #[MapEntity(mapping: ['seasonID' => "id"])] Season $season
    ): Response
    {
        return $this->render('program/season_show.html.twig', ['season' => $season, "program" => $program]);
    }

    #[Route('/{programSlug}/season/{seasonID}/episode/{episodeSlug}', methods: ["GET", "POST"], requirements:['seasonID' => '\d+'], name: 'episode_show' )]
    public function showEpisode(
        #[MapEntity(mapping: ['programSlug' => "slug"])] Program $program,
        #[MapEntity(mapping: ['seasonID' => "id"])] Season $season,
        #[MapEntity(mapping: ['episodeSlug' => "slug"])] Episode $episode,
        Request $request, 
        EntityManagerInterface $entityManagerInterface,
        CommentRepository $commentRepository,
    ): Response
    {
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $comment->setEpisode($episode);
            $comment->setAuthor($this->security->getUser());
            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("program_episode_show", [
                "programSlug" => $program->getSlug(), 
                "seasonID" => $season->getID(), 
                "episodeSlug" => $episode->getSlug()]);
        }

        $comments = $commentRepository->findBy(["episode" => $episode]);

        return $this->render('program/episode_show.html.twig', [
            'season' => $season, 
            "program" => $program, 
            "episode" => $episode, 
            "formContent" => $formComment,
            "comments" => $comments
        ]);
    }

}
