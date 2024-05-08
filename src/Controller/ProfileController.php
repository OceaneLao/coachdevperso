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
    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function show(
        EntityManagerInterface $entityManagerInterface
    ) : Response
        {
            $user = $this->getUser();
            $profileRepository = $entityManagerInterface->getRepository(Profile::class);
            $profiles = $profileRepository->findBy(['user'=>$user->getId()]);

            return $this->render('profile/index.html.twig', [
                'profiles' => $profiles,
            ]);
        }

    #[Route('/profile/new', name: 'app_profile_new', methods: ['GET', 'POST'])]
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
                // Cheminement de l'image dans le dossier uploads
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
        return $this->render('profile/new.html.twig', [
            'profiles' => $profiles,
            'form' => $form,
        ]);
    }

    //Modifier le profil
    #[Route('/profile/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
    ): Response
    {
        //Récupérer toutes les données dans le Repository
        $user = $this->getUser();
        $profileRepository = $entityManagerInterface->getRepository(Profile::class);
        $profile = $profileRepository->findOneBy(['user'=>$user]);
        
        // Créer le formulaire pour modifier le profil
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()){
            // Récupérer l'ancienne image
            $oldPicture = $profile->getPicture();
                if($oldPicture){
                    $profile->setPicture("");
                    // dd($oldPicture);
                    $entityManagerInterface->persist($profile);
                    $entityManagerInterface->flush();
                }

            // Récupérer la nouvelle image soumise dans le formulaire
            $newPicture = $form->get('picture')->getData();
            if($newPicture){
                // Génrer un nom unique pour la nouvelle image
                $newPictureFileName = uniqid() . '.' . $newPicture->guessExtension();
                
                // Déplacer la nouvelle image vers le répertoire de stockage
                    $newPicture->move(
                        $this->getParameter('profile_directory'),
                        $newPictureFileName
                    );
                
                $profile->setPicture($newPictureFileName);
                }

        // Récupérer la description
        $description = $form->get('description')->getData();
        $profile->setDescription($description);

        $entityManagerInterface->persist($profile);
        $entityManagerInterface->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }
}