<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentFormType;
use App\Form\AppointmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(
        EntityManagerInterface $entityManagerInterface,
    ): Response
    {
        // Récupérer tous les rendez-vous associés à des utilisateurs
        $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
        $appointments = $appointmentRepository->findBy(['isAvailable' => false], ['id' => 'ASC']);

        return $this->render('admin/index.html.twig', [
            'appointments' => $appointments,
        ]);
    }

    // Ajouter des créneaux de RDV
    #[Route('/admin/add-appointment', name:'app_admin_appointment')]
    public function addAppointment(
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ): Response
    {
        $appointment = new Appointment();
        $form = $this->createForm(AppointmentFormType::class,$appointment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $appointment->setStartedAt(
                $form->get('startedAt')->getData()
            );

            $entityManagerInterface ->persist($appointment);
            $entityManagerInterface ->flush();
        }

    return $this->render('admin/add-appointment.html.twig', [
        'appointmentForm' => $form,
    ]);
    }

    // Supprimer des créneaux de RDV
    #[Route('/admin/delete-appointment/{id}', name:'app_admin_appointment_delete', methods:['POST','GET'])]
    public function deleteAppointment(
        EntityManagerInterface $entityManagerInterface,
        Appointment $appointment
    ): Response
    {
        // Récupérer l'id de l'appointment
        $appointmentId = $appointment->getId();
        $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
        $appointment = $appointmentRepository->find($appointmentId);
        
        $entityManagerInterface ->remove($appointment);
        $entityManagerInterface ->flush();

    return $this->render('admin/delete-appointment.html.twig', [
        'id' => $appointmentId
    ]);
    }
}
