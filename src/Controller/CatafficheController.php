<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/affichage')]
final class CatafficheController extends AbstractController
{
    #[Route(name: 'app_categorie_affichage', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/affichage.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/accueil', name: 'app_index', methods: ['GET'])]
    public function indexx(): Response
    {
        return $this->render('home/index.html.twig');
    }
}

