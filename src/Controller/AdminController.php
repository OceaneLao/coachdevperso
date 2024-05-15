<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Profile;
use App\Form\AppointmentFilterType;
use App\Form\AppointmentFormType;
use App\Repository\AppointmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin', methods:['GET'])]
    public function index(
        EntityManagerInterface $entityManagerInterface,
    ): Response
    {
        $user = $this->getUser();
        
        // Récupérer tous les rendez-vous associés à des utilisateurs
        $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
        $appointments = $appointmentRepository->findBy(['isAvailable' => false], ['id' => 'ASC']);

        // Tableau pour stocker les profils des utilisateurs
        $profiles = [];

        // Récupérer les profils des utilisateurs associés aux rendez-vous
        foreach ($appointments as $appointment){
            $user = $appointment->getUser();
            if ($user) {
                // Vérifier su le profil de l'utilisateur n'a pas été ajoutée au tableau
                if(!isset($profiles[$user->getId()])){
                    $profileRepository = $entityManagerInterface->getRepository(Profile::class);
                    $profile = $profileRepository->findOneBy(['user'=>$user]);
                    $profiles[$user->getId()] = $profile ?? null;
                }
            }
        }

        return $this->render('admin/index.html.twig', [
            'appointments' => $appointments,
            'profiles' => $profiles,
        ]);
    }


    // Ajouter des créneaux de RDV
    #[Route('/admin/add-appointment', name:'app_admin_appointment', methods:['GET', 'POST'])]
    public function addAppointment(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        AppointmentRepository $appointmentRepository
    ): Response
    {
       // Récupérer toutes les données dans le Repository
       $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
       $appointments = $appointmentRepository->findAll();
       $appointment = new Appointment();

       // Filtrer les RDV par année et par mois
       $filterForm = $this->createForm(AppointmentFilterType::class, $appointment);
       $filterForm->handleRequest($request);

        $dates = [];
        // Formulaire pour filtrer les dates du mois de Mai
        if($filterForm->isSubmitted() && $filterForm->isValid()){
            $formData = $filterForm->get('startedAt')->getData();
            $year = $formData->format('Y');
            $month = $formData->format('m');
        
        // Afficher les dates liées au mois sélectionné 
        $startDate = new \DateTime($year . '-' . $month . '-01');
        $endDate = new \DateTime($year . '-' . $month . '-31');

        $endDate = clone $startDate;
        $endDate->modify('last day of this month');

        $currentDate = clone $startDate;
        while ($currentDate <= $endDate){
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->modify('+1 day');
        }
      }

    return $this->render('admin/add-appointment.html.twig', [
        'appointments' => $appointments,
        'filterForm' => $filterForm,
        'dates' => $dates,
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
        
        // Supprimer le rendez-vous dans la BDD
        $entityManagerInterface ->remove($appointment);
        $entityManagerInterface ->flush();

    return $this->render('admin/delete-appointment.html.twig', [
        'id' => $appointmentId,
    ]);
    }
}
