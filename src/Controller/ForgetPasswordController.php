<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ForgetPasswordController extends AbstractController
{
    #[Route('/forgetpassword', name: 'forgetpassword')]
    public function index(): Response
    {
        return $this->render('forget_password/index.html.twig', [
            'controller_name' => 'ForgetPasswordController',
        ]);
    }
}
