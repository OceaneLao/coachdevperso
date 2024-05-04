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
        SluggerInterface $slugger,
    ): Response
    {
        $profile = new Profile();
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
                    dd($fileName);
            }
        $entityManagerInterface->persist($profile);
        $entityManagerInterface->flush();
        
            return $this->render('profile/index.html.twig', [
            'profile' => $profile,
            'form' => $form,
            ]);
        }
    }
}