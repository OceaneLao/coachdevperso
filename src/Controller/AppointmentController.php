<?php
namespace App\Controller;

use App\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AppointmentController extends AbstractController
{
    #[Route('/appointment', name: 'app_appointment')]
    public function displayAppointment(
        EntityManagerInterface $entityManagerInterface
        ): Response
    {
        // Récupérer toutes les données dans le Repository
        $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
        $appointments = $appointmentRepository->findAll();

        return $this->render('appointment/index.html.twig', [
            'appointments' => $appointments,
        ]);
    }

    #[Route('appointment/submit', name: 'app_appointment_submit', methods: ['POST', 'GET'])]
    public function submitAppointment(
        // Request $request,
        // EntityManagerInterface $entityManager,
        // UserInterface $user
        ): Response
    {
    //     // Récupérer l'id de l'utilisateur actuellement authentifié
    //    $user = $this->getUser();

    //    //Vérifier si un utilisateur est authentifié
    //    if (!$user) {
    //     // Gérer le cas où un utilisateur n'est pas authentifié
    //     return $this->redirectToRoute('app_login');
    //    }
       
    //    //Récupérer la date et l'horaire sélectionnés par l'utilisateur
    //    $selectedDate = $request->request->get('selected_date');
    //    $selectedSchedule = $request->request->get('selected_schedule');

    //    // Vérifier si les données sont valides
    //    if (!$selectedDate || !$selectedSchedule){
    //     return $this->redirectToRoute('app_appointment');
    //    }

    //    //Créer une nouvelle instance d'Appointment
    //    $appointment = new Appointment();
    //    // Définir l'utilisateur associé à cette réservation
    //    $appointment->setUser($user); 
       
    //    $startDate =\DateTimeImmutable::createFromFormat('Y-m-d H:i', $selectedDate . ' ' . $selectedSchedule);
    //    if ($startDate instanceof \DateTimeImmutable){
    //    // Définir la date et l'horaire de la réservation
    //    $appointment->setStartedAt($startDate);
    //     }
    //    // Enregistrer la réservation en base de données
    //    $entityManager->persist($appointment);
    //    $entityManager->flush();

    return $this->render('appointment/submit.html.twig');
    }
    
}
