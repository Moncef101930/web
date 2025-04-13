<?php
namespace App\Controller;


use App\Entity\evenement;
use App\Form\evenementType1;
use App\Repository\evenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
final class evenementControllerAdmin extends AbstractController
{
    #[Route(name: 'app_evenement_indexy', methods: ['GET'])]
    public function index(evenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/Admin.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/{id}/associer', name: 'associer_categorie', methods: ['GET', 'POST'])]
    public function associer(Request $request, evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(evenementType1::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($evenement->getCategories() as $categorie) {
                if ($categorie->getId() === null) {
                    $entityManager->persist($categorie);
                }
            }

            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_indexy', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/eventcat.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }
}
