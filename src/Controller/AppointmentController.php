<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Review;
use App\Entity\User;
use App\Form\AppointmentFormType;
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
        Request $request,
        Appointment $appointment,
        EntityManagerInterface $entityManagerInterface,
    ): Response {

        $form = $this->createForm(AppointmentFormType::class, $appointment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Rendre l'appointment disponible
            $appointment->setAvailable(false);
        
            // Mettre à jour la BDD
            $entityManagerInterface->persist($appointment);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_user');
        }

        return $this->render('appointment/edit.html.twig', [
            'appointment' => $appointment,
            'form' => $form,
        ]);
    }

    private function updateRelatedAppointmentsAvailability(Appointment $appointment, EntityManagerInterface $entityManager)
    {
        // Récupérer les rendez-vous associés à la même date et la même heure que $appointment
        $relatedAppointments = $entityManager->getRepository(Appointment::class)->findBy([
            'startedAt' => $appointment->getStartedAt(),
            'endedAt' => $appointment->getEndedAt(),
        ]);

        // Mettre à jour la disponibilité de chaque rendez-vous associé
        foreach ($relatedAppointments as $relatedAppointment) {
            $relatedAppointment->setAvailable(true);
        }

        // Persistez les changements
        $entityManager->flush();
    }

    // Annuler un RDV
    #[Route('/appointment/{id}/cancel', name: 'app_appointment_cancel', methods: ['GET', 'POST'])]
    public function cancelAppointment(Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        $appointment->setAvailable(true);
        $entityManager->flush();

        // Rediriger l'utilisateur vers une page de confirmation ou une autre page pertinente
        return $this->redirectToRoute('app_user',[
            'id' => $appointment->getId(),
        ]);
    }
}