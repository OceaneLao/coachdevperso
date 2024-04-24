<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\User;
use App\Repository\AppointmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AppointmentController extends AbstractController
{
    #[Route('/appointment', name: 'app_appointment')]
    public function displayAppointment(
        EntityManagerInterface $entityManagerInterface
    ): Response {
        // Récupérer toutes les données dans le Repository
        $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
        $appointments = $appointmentRepository->findAll();
        // dd($appointments);

        return $this->render('appointment/index.html.twig', [
            'appointments' => $appointments,
        ]);
    }

    #[Route('appointment/submit/{id}', name: 'app_appointment_submit', methods: ['POST', 'GET'])]
    public function submitAppointment($id): Response
    {
        dd($id);
        // Récupérer l'id de l'utilisateur actuellement authentifié
        $user = $this->getUser();

        //Vérifier si un utilisateur est authentifié
        if (!$user instanceof User) {
            // Gérer le cas où un utilisateur n'est pas authentifié
            return $this->redirectToRoute('app_login');
            // Si l'utilisateur est authentifié
        }

        // Récupérer l'appointment
        // attribuer l'appointment au user
        // rendre l'appointment indisponible

        return $this->render('appointment/submit.html.twig');
    }
}
