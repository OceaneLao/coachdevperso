<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            if($this->isGranted('ROLE_ADMIN')){
                return $this->redirectToRoute('app_admin');
            } else {
                return $this->redirectToRoute('app_user');
            }
        }
        // Erreur de connexion si l'utilisateur est déjà authentifié
        $error = $authenticationUtils->getLastAuthenticationError();
        // E-mail saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->render('home/index.html.twig');
    }
}
