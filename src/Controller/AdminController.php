<?php

namespace App\Controller;

use App\Entity\Appointment;
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

        // Date de création du RDV avec date et jour d'aujourd'hui
        $appointment->setCreatedAt(new \DateTimeImmutable('now'));
        // Définir l'horaire sur une heure spécifique sans ajouter la date
        $startedAt = \DateTimeImmutable::createFromFormat('H:i', '09:00');
        $appointment->setStartedAt($startedAt);
        // Ajouter une heure à endedAt pour définir l'horaire de fin
        $endedAt = clone $startedAt->modify('+1 hour');
        $appointment->setEndedAt($endedAt);

        $form = $this->createForm(AppointmentFormType::class,$appointment);
        $form->handleRequest($request);

        // Filtrer les RDV par année et par mois
        $filterForm = $this->createForm(AppointmentFilterType::class, $appointment);
        $filterForm->handleRequest($request);

        // Formulaire pour ajouter un RDV
        if($form->isSubmitted() && $form->isValid()){ 
            // Vérifier si un RDV avec le même horaire de début existe pour la même date
            $existAppointment = $entityManagerInterface->getRepository(Appointment::class)->findOneBy(['startedAt' => $appointment->getStartedAt()
            ]);

            if($existAppointment){
                $this->addFlash('error', 'Un RDV avec cet horaire existe déjà pour cette date.');
            }else{
            $appointment->setStartedAt(
                $form->get('startedAt')->getData()
            );
            $entityManagerInterface ->persist($appointment);
            $entityManagerInterface ->flush();

            $this->addFlash('success', 'Le RDV a été ajouté avec succès.');
            }
        }

        //Formulaire pour filtrer
        if($filterForm->isSubmitted() && $filterForm->isValid()){
            $startDate = $filterForm->get('startedAt')->getData();
            $year = $startDate->format('Y');
            $month = $startDate->format('m');
            $result = [];
            foreach ($appointments as $a) {
                // Extraire l'année et le mois de chaque rendez-vous
                $appointmentYear = $a->getStartedAt()->format('Y');
                $appointmentMonth = $a->getStartedAt()->format('m');
                
                // Vérifier si l'année ou le mois correspond à ceux spécifiés dans le filtre
                if($appointmentMonth){
                    if ($appointmentYear == $year && $appointmentMonth == $month) {
                        $result[] = $a;
                    }
                } else {
                    if ($appointmentYear == $year) {
                        $result[] = $a;
                    }
                }
            }
        }
        // dd($result);

    return $this->render('admin/add-appointment.html.twig', [
        'appointments' => $result,
        'appointmentForm' => $form,
        'filterForm' => $filterForm,
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
