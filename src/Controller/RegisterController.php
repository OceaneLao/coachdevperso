<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/register')]
class RegisterController extends AbstractController
{
    #[Route('/', name: 'register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('register_confirm', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('register/index.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/confirm', name: 'register_confirm', methods: ['GET'])]
    public function show(): Response
    {
        return $this->render('register/confirm.html.twig');
    }
}