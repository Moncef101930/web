<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class InscriptionController extends AbstractController
{
    // Route pour afficher le formulaire d'inscription
    #[Route('/inscription', name: 'app_inss')]
    public function index(): Response
    {
        return $this->render('inscription/index.html.twig', [
            'controller_name' => 'InscriptionController',
        ]);
    }

    // Route pour afficher la page user.html.twig
    #[Route('/userr', name: 'app_user')]
    public function user(): Response
    {
        return $this->render('evenement/user.html.twig');
    }

    // Méthode pour rediriger vers la page 'user.html.twig' 
    #[Route('/inscription/redirect-to-user', name: 'app_redirect_to_user')]
    public function redirectToUser(): Response
    {
        // Logique d'inscription ou d'autres processus ici...

        // Redirection vers la page 'user.html.twig'
        return $this->redirectToRoute('app_user');
    }
}
