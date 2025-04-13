<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/in')]
class inscController extends AbstractController
{
    #[Route(name: 'app_inss', methods: ['GET'])]
    public function index(): Response  // Renamed the method to index
    {
        return $this->render('home/ins.html.twig'
         
        );
    }

    #[Route('/affichage', name: 'app_affichage')]
    public function affichage(): Response  // Renamed the method to affichage
    {
        return $this->render('categorie/affichage.html.twig');
    }
}
