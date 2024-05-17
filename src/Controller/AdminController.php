<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Profile;
use App\Form\AppointmentFilterType;
use App\Form\AppointmentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin', methods: ['GET'])]
    public function index(
        EntityManagerInterface $entityManagerInterface,
    ): Response {
        $user = $this->getUser();

        // Récupérer tous les rendez-vous associés à des utilisateurs
        $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
        $appointments = $appointmentRepository->findBy(['isAvailable' => false], ['id' => 'ASC']);

        // Tableau pour stocker les profils des utilisateurs
        $profiles = [];

        // Récupérer les profils des utilisateurs associés aux rendez-vous
        foreach ($appointments as $appointment) {
            $user = $appointment->getUser();
            if ($user) {
                // Vérifier su le profil de l'utilisateur n'a pas été ajoutée au tableau
                if (!isset($profiles[$user->getId()])) {
                    $profileRepository = $entityManagerInterface->getRepository(Profile::class);
                    $profile = $profileRepository->findOneBy(['user' => $user]);
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
    #[Route('/admin/add-appointment', name: 'app_admin_appointment', methods: ['GET', 'POST'])]
    public function addAppointment(
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ): Response {
        // Filtrer les RDV par année et par mois
        $filterForm = $this->createForm(AppointmentFilterType::class);
        $filterForm->handleRequest($request);

        $createdRDV = [];
        $dates = [];
        // Formulaire pour filtrer les dates du mois
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $formData = $filterForm->get('startedAt')->getData();
            $year = $formData->format('Y');
            $month = $formData->format('m');

            $createdRDV = $entityManagerInterface->getRepository(Appointment::class)->filterByYearAndMonth($year, $month);

            // Afficher les dates liées au mois et à l'année sélectionnés 
            $startDate = new \DateTime($year . '-' . $month . '-01');
            $endDate = new \DateTime($year . '-' . $month . '-31');

            // Afficher le dernier jour du mois
            $endDate = clone $startDate;
            $endDate->modify('last day of this month');

            // Affcher toutes les dates
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $dates[] = $currentDate->format('Y-m-d');
                $currentDate->modify('+1 day');
            }
        }

        // Ajouter un RDV
        $appointment = new Appointment();

        // Date de création du RDV avec date et jour d'aujourd'hui
        $appointment->setCreatedAt(new \DateTimeImmutable('now'));

        $form = $this->createForm(AppointmentFormType::class, $appointment);
        $form->handleRequest($request);

        // Formulaire pour ajouter un RDV
        if ($form->isSubmitted() && $form->isValid()) {

            // Vérifier si un RDV avec le même horaire de début existe pour la même date
            $existAppointment = $entityManagerInterface->getRepository(Appointment::class)->findOneBy([
                'startedAt' => $appointment->getStartedAt()
            ]);

            if ($existAppointment) {
                $existAppointment->getStartedAt();
                $this->addFlash('error', 'Un RDV avec cet horaire existe déjà pour cette date.');
            } else {
                $appointment->setStartedAt(
                    $form->get('startedAt')->getData()
                );

                $entityManagerInterface->persist($appointment);
                $entityManagerInterface->flush();

                $this->addFlash('success', 'Le RDV a été ajouté avec succès.');
            }
        }
        return $this->render('admin/add-appointment.html.twig', [
            'filterForm' => $filterForm,
            'appointments' => $createdRDV,
            'dates' => $dates,
            'form' => $form,
        ]);
    }

    // Supprimer des créneaux de RDV
    #[Route('/admin/delete-appointment/{id}', name: 'app_admin_appointment_delete', methods: ['POST', 'GET'])]
    public function deleteAppointment(
        EntityManagerInterface $entityManagerInterface,
        Appointment $appointment
    ): Response {
        // Récupérer l'id de l'appointment
        $appointmentId = $appointment->getId();
        $appointmentRepository = $entityManagerInterface->getRepository(Appointment::class);
        $appointment = $appointmentRepository->find($appointmentId);

        // Supprimer le rendez-vous dans la BDD
        $entityManagerInterface->remove($appointment);
        $entityManagerInterface->flush();

        return $this->render('admin/delete-appointment.html.twig', [
            'id' => $appointmentId,
        ]);
    }
}