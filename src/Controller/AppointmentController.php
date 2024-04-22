<?php
namespace App\Controller;

use App\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
}
