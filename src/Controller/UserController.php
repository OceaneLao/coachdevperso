<?php

namespace App\Controller;

use App\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(
        EntityManagerInterface $entityManagerInterface,
    ): Response
    {
        $user = $this->getUser();
        // dd($user);

        $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
        $appointments = $appointmentRepository->findBy(['user'=>$user->getId()]);
        // dd($appointments);

        return $this->render('user/index.html.twig', [
            'user'=> $user,
            'appointments'=> $appointments,
        ]);
    }

    #[Route('/is-authenticated', name: 'app_is_authenticated')]
    public function isAuthenticated(): Response
    {
        // Vérifier si l'utilisateur est authentifié
        $isAuthenticated = $this->isGranted('IS_AUTHENTICATED_FULLY');

        //Retourner la réponse appropriée (true ou false)
        return new Response($isAuthenticated ? 'true' : 'false');
    }
}
