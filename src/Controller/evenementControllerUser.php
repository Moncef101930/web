<?php
namespace App\Controller;
use App\Entity\evenement;
use App\Form\evenementType;
use App\Repository\evenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class evenementControllerUser extends AbstractController
{
    #[Route(name: 'app_evenement_indexa', methods: ['GET'])]
    public function index(evenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/user.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }
}