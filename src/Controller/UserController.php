<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/my-profile', name: "my-profile")]
    public function myProfil(): Response
    {
        return $this->render("user/my-profile.html.twig");
    }
}