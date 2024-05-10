<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppointmentController extends AbstractController
{
    #[Route('/appointment', name: 'app_appointment')]
    public function displayAppointment(
        EntityManagerInterface $entityManagerInterface
    ): Response {
        // Récupérer toutes les données dans le Repository
        $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
        $appointments = $appointmentRepository->findAll();

        return $this->render('appointment/index.html.twig', [
            'appointments' => $appointments,
        ]);
    }

    // Symfony reconnaît les accolades {id} comme une variable => $id
    #[Route('appointment/submit/{id}', name: 'app_appointment_submit', methods: ['POST', 'GET'])]
    public function submitAppointment(
        EntityManagerInterface $entityManagerInterface,
        $id
    ): Response {
        // Récupérer l'id de l'utilisateur actuellement authentifié
        $user = $this->getUser();

        // Vérifier si un utilisateur est authentifié
        if (!$user instanceof User) {
            // Gérer le cas où un utilisateur n'est pas authentifié
            return $this->redirectToRoute('app_login');
            // Si l'utilisateur est authentifié
        }

        // Récupérer l'appointment
        $appointmentId = $id;
        $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
        $appointment = $appointmentRepository->find($appointmentId);
        
        // Attribuer l'appointment au user
        $appointment->setUser($user);
        
        // Rendre l'appointment indisponible
        $isAvailable = $appointment->isAvailable();
        if ($isAvailable) {
            $appointment->setAvailable(false);
        }

        // Mettre à jour dans la BDD
        $entityManagerInterface->persist($appointment);
        $entityManagerInterface->flush();

        return $this->render('appointment/submit.html.twig', [
            'appointment' => $appointment,
        ]);
    }

    // Modifier un RDV
    #[Route('appointment/edit/{id}', name: 'app_appointment_edit', methods: ['GET', 'POST'])]
    public function editAppointment(
        EntityManagerInterface $entityManagerInterface,
        Appointment $appointment,
        Request $request,
        $id,
    ): Response {

        // Récupérer toutes les données dans le Repository
        $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
        $appointments = $appointmentRepository->findBy(array("isAvailable"=>true));

        if ($request->getMethod() === "GET"){
            return $this->render('appointment/edit.html.twig', [
                'appointments' => $appointments,
                'oldAppointment' => $appointment
            ]);
        }

        //Récupérer l'ancien appointment et l'annuler 
        $oldAppointmentId = (int)$request->query->get('oldAppointment');
        $oldAppointment = $appointmentRepository->find($oldAppointmentId);
        $oldAppointment->setUser(null);
        $oldAppointment->setAvailable(true);
        
        // Attribuer l'id appointment à l'utilisateur
        $user = $this->getUser();
        $appointment->setUser($user);
        $appointment->setAvailable(false);

        // Mettre à jour dans la BDD
        $entityManagerInterface->flush();

        return $this->redirectToRoute('app_user');
    }

    // Annuler un RDV
    #[Route('/appointment/cancel/{id}', name: 'app_appointment_cancel', methods: ['GET', 'POST'])]
    public function cancelAppointment(Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        $appointment->setUser(null);
        $appointment->setAvailable(true);
        $entityManager->flush();

        return $this->render('appointment/cancel.html.twig',[
            'id' => $appointment->getId(),
        ]);
    }
}