<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/comment', name: 'comment_')]
class CommentController extends AbstractController
{
    #[Route('/{id}', name: 'delete')]
    public function delete(
        Comment $comment, 
        Request $request, 
        EntityManagerInterface $entityManager): Response
    {
        
        $episode = $comment->getEpisode();
        $season = $episode->getSeason();
        $program = $season->getProgram();

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        $this->addFlash('warning', 'The comment has been deleted');

        return $this->redirectToRoute("program_episode_show", [
            "programSlug" => $program->getSlug(), 
            "seasonID" => $season->getID(), 
            "episodeSlug" => $episode->getSlug()]);
    }
}
