<?php

namespace App\Controller;

use App\Entity\evenement;
use App\Form\evenementType;
use App\Repository\evenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/evenement')]
final class evenementController extends AbstractController
{
    #[Route('/', name: 'app_evenement_indexz', methods: ['GET'])]
    public function index(Request $request, evenementRepository $evenementRepository): Response
    {
        $searchTerm = $request->query->get('search', '');

        // Recherche des événements avec un terme de recherche
        if (!empty($searchTerm)) {
            $evenements = $evenementRepository->createQueryBuilder('e')
                ->leftJoin('e.categories', 'c')
                ->where('e.nom LIKE :search OR e.lieu LIKE :search OR c.nom LIKE :search')
                ->setParameter('search', '%' . $searchTerm . '%')
                ->getQuery()
                ->getResult();
        } else {
            $evenements = $evenementRepository->findAll();
        }

        // Recherche de l'événement le plus populaire (celui avec le plus de catégories)
        $mostFamousEvent = null;
        $maxCategories = -1;
        foreach ($evenements as $evenement) {
            if (count($evenement->getCategories()) > $maxCategories) {
                $mostFamousEvent = $evenement;
                $maxCategories = count($evenement->getCategories());
            }
        }
        $mostFamousEventName = $mostFamousEvent ? $mostFamousEvent->getNom() : null;

        // Compte des événements par catégorie
        $categoryCountMap = [];
        foreach ($evenements as $evenement) {
            foreach ($evenement->getCategories() as $categorie) {
                $categoryName = $categorie->getNom();
                if (!isset($categoryCountMap[$categoryName])) {
                    $categoryCountMap[$categoryName] = 0;
                }
                $categoryCountMap[$categoryName]++;
            }
        }

        // Envoie des données au template Twig
        return $this->render('evenement/indexx.html.twig', [
            'evenements' => $evenements,
            'mostFamousEventName' => $mostFamousEventName,
            'categoriesNames' => array_keys($categoryCountMap),
            'eventCountsByCategory' => array_values($categoryCountMap),
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/new', name: 'app_evenement_creer', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new evenement();
        $form = $this->createForm(evenementType::class, $evenement);
        $form->handleRequest($request);

        // Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_indexz', [], Response::HTTP_SEE_OTHER);
        }

        // Rendu du formulaire
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

        // Traitement du formulaire d'édition
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_indexz', [], Response::HTTP_SEE_OTHER);
        }

        // Rendu du formulaire d'édition
        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        // Vérification de la suppression
        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_indexz', [], Response::HTTP_SEE_OTHER);
    }

    // 🌟 Nouvelle méthode pour générer le PDF des événements
    #[Route('/export/pdf', name: 'app_evenement_pdf', methods: ['GET'])]
    public function exportPdf(evenementRepository $evenementRepository): Response
    {
        // 1. Récupérer tous les événements
        $evenements = $evenementRepository->findAll();

        // 2. Configurer Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        // 3. Générer le HTML du PDF
        $html = $this->renderView('evenement/pdf.html.twig', [
            'evenements' => $evenements,
        ]);

        $dompdf->loadHtml($html);

        // (Optionnel) Taille du papier et orientation
        $dompdf->setPaper('A4', 'portrait');

        // 4. Générer le PDF
        $dompdf->render();

        // 5. Envoyer le PDF en téléchargement
        return new Response(
            $dompdf->stream('evenements.pdf', ["Attachment" => true]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }


}
