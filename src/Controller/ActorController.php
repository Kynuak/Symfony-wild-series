<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Form\ActorType;
use App\Repository\ActorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/actor', name: 'actor_')]
class ActorController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ActorRepository $actorRepository): Response
    {

        $actors = $actorRepository->findAll();
        return $this->render('actor/index.html.twig', [
            'actors' => $actors,
        ]);
    }


    #[Route('/new', name: 'new')]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManagerInterface,
        SluggerInterface $slugger,
    ): Response
    {

        $actor = new Actor();
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($actor->getName());
            $actor->setSlug($slug);
            $entityManagerInterface->persist($actor);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'The new program has been created');

            return $this->redirectToRoute('actor_index');
        }


        return $this->render('actor/new.html.twig', ['form' => $form]);
    }

    
    #[Route('/{actorID}', methods: ['GET'], requirements: ['actorID' => "\d+"], name: 'show')]
    public function show(
        #[MapEntity(mapping: ['actorID' => 'id'])] Actor $actor
    ): Response
    {
        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
        ]);
    }
}
