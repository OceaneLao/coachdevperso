<?php

namespace App\Controller;

use App\Controller\Admin\AppointmentCrudController;
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
    ): Response
    {   
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
        if ($isAvailable){
            $appointment->setAvailable(false);
        }
    
        // Mettre à jour dans la BDD
        $entityManagerInterface->persist($appointment);
        $entityManagerInterface->flush();

        return $this->render('appointment/submit.html.twig',[
            'appointment' => $appointment,
        ]);
    }

     // Modifier un RDV
     #[Route('appointment/edit', name: 'app_appointment_edit', methods: ['GET'])]
     public function editAppointment(
        Request $request,
        EntityManagerInterface $entityManagerInterface
     ) : Response
     {
        // Récupérer l'appointment lié à l'utilisateur
        $user = $this->getUser();
        $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
        $editAppointment = $appointmentRepository->findOneBy(['user'=>$user->getId()]);
        // dd($editAppointment);

        // Attribuer un nouveau RDV à l'utilisateur
        
        // Rendre l'appointment disponible

        // Mettre à jour la BDD
        
         return $this->render('appointment/edit.html.twig',[
             'appointment' => $editAppointment,
         ]);
     }
     
    // Annuler un RDV
    #[Route('appointment/cancel', name: 'app_appointment_cancel', methods: ['GET'])]
    public function cancelAppointment(
    ) : Response
    {
        return $this->render('appointment/edit.html.twig');
    }
}
