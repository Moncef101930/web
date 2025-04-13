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

#[Route('/evenement')]
final class evenementController extends AbstractController
{
    #[Route(name: 'app_evenement_indexz', methods: ['GET'])]
    public function index(evenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/indexx.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evenement_creer', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new evenement();
        $form = $this->createForm(evenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_indexz', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(evenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_indexz', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

#[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
public function delete(Request $request, evenement $evenement, EntityManagerInterface $entityManager): Response
{
    // Debugging : vérifier si l'événement est bien récupéré
    if (!$evenement) {
        throw $this->createNotFoundException('No event found for id ' . $request->get('id'));
    }

    // Vérification du token CSRF
    if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->request->get('_token'))) {
        // Suppression de l'événement
        $entityManager->remove($evenement);
        $entityManager->flush();
    }

    // Redirection après suppression
    return $this->redirectToRoute('app_evenement_indexz', [], Response::HTTP_SEE_OTHER);
}
}
