<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        SluggerInterface $slugger
    ): Response
    {
        $user = $this->getUser();
        $profileRepository = $entityManagerInterface->getRepository(Profile::class);
        $profiles = $profileRepository->findBy(['user'=>$user->getId()]);
        //Vérifier si un profil existe déjà pour cet utilisateur
        $existingProfile = $entityManagerInterface->getRepository(Profile::class)->findOneBy(['user'=>$user]);
        if($existingProfile){
            $this->addFlash('error', 'Un profil existe déjà pour votre compte.');
        }

        $profile = new Profile();
        $profile->setUser($user);
       
    $form = $this->createForm(ProfileType::class, $profile);
    $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('picture')->getData();
            if($image){
                $originalName = pathinfo(
                $image->getClientOriginalName(), PATHINFO_FILENAME
                );
                $nameSlugged = $slugger->slug($originalName);
                $fileName = $nameSlugged . '-' . uniqid() . '.' . $image->guessExtension();
                //dd($fileName);
                // Cheminement de l'image
                try {
                    $image->move(
                        $this->getParameter('profile_directory'),
                        $fileName
                    );
                }
                catch(\Throwable $th){
                    throw $th;
                }
                $profile->setPicture($fileName); 
                // dd($profile);
            }
            // Récupérer la description
            $description = $form->get('description')->getData();
            $profile->setDescription($description);

            // Enregistrer le profil
            $entityManagerInterface->persist($profile);
            $entityManagerInterface->flush();

            $this->addFlash('success', 'Votre profil a été créé avec succès !');

            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile/index.html.twig', [
            'profiles' => $profiles,
            'form' => $form,
        ]);
    }
}