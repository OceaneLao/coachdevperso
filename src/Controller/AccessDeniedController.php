<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccessDeniedController extends AbstractController
{
    #[Route('/access_denied', name: 'app_access_denied')]
    public function accessDenied(): Response
    {
        return $this->render('access_denied/index.html.twig', [
            'message' => "Vous n'avez pas l'autorisation d'accéder à l'espace administrateur.",
        ]);
    }
}
