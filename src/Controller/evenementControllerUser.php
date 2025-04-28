<?php

namespace App\Controller;

use App\Entity\evenement;
use App\Repository\evenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
final class evenementControllerUser extends AbstractController
{
    #[Route(name: 'app_evenement_indexa', methods: ['GET'])]
    public function index(Request $request, evenementRepository $evenementRepository): Response
    {
        // Récupérer le terme de recherche
        $searchTerm = $request->query->get('search');
        
        // Récupérer tous les événements
        $allEvenements = $evenementRepository->findAll();
        $mostFamousEventName = $this->getMostFamousEvent($allEvenements);

        // Filtrer les événements si nécessaire
        if ($searchTerm) {
            $evenements = $evenementRepository->createQueryBuilder('e')
                ->leftJoin('e.categories', 'c')
                ->where('e.nom LIKE :term')
                ->orWhere('e.lieu LIKE :term')
                ->orWhere('e.description LIKE :term')
                ->orWhere('c.nom LIKE :term')
                ->setParameter('term', '%'.$searchTerm.'%')
                ->getQuery()
                ->getResult();
        } else {
            $evenements = $allEvenements;
        }

        // Si aucun événement n'est trouvé
        if (empty($evenements)) {
            $this->addFlash('notice', 'Aucun événement trouvé.');
        }

        // Préparer les données pour le graphique
        $categoryCountMap = [];
        foreach ($allEvenements as $evenement) {
            foreach ($evenement->getCategories() as $categorie) {
                $categoryName = $categorie->getNom();
                if (!isset($categoryCountMap[$categoryName])) {
                    $categoryCountMap[$categoryName] = 0;
                }
                $categoryCountMap[$categoryName]++;
            }
        }

        // Créer les catégories et le comptage des événements par catégorie
        $categoriesNames = array_keys($categoryCountMap);
        $eventCountsByCategory = array_values($categoryCountMap);

        // Renvoyer la vue avec toutes les données nécessaires
        return $this->render('evenement/user.html.twig', [
            'evenements' => $evenements,
            'mostFamousEventName' => $mostFamousEventName,
            'searchTerm' => $searchTerm,
            'categoriesNames' => $categoriesNames,
            'eventCountsByCategory' => $eventCountsByCategory,
        ]);
    }

    private function getMostFamousEvent(array $evenements): ?string
    {
        // Calcul du nombre d'événements par nom
        $eventCounts = [];
        foreach ($evenements as $evenement) {
            if ($evenement->getCategories()->count() > 0) {
                $eventName = $evenement->getNom();
                if (!isset($eventCounts[$eventName])) {
                    $eventCounts[$eventName] = 0;
                }
                $eventCounts[$eventName]++;
            }
        }

        // Si aucun événement n'a de catégorie, retour de null
        if (empty($eventCounts)) {
            return null;
        }

        // Trier les événements par popularité et retourner le plus populaire
        arsort($eventCounts);
        return array_key_first($eventCounts);
    }

    #[Route('/userfire', name: 'app_user_fire', methods: ['GET'])]
    public function userFire(evenementRepository $evenementRepository): Response
    {
        // Récupérer tous les événements pour les statistiques
        $allEvenements = $evenementRepository->findAll();
        
        // Préparer les données pour le graphique
        $categoryCountMap = [];
        foreach ($allEvenements as $evenement) {
            foreach ($evenement->getCategories() as $categorie) {
                $categoryName = $categorie->getNom();
                if (!isset($categoryCountMap[$categoryName])) {
                    $categoryCountMap[$categoryName] = 0;
                }
                $categoryCountMap[$categoryName]++;
            }
        }

        $categoriesNames = array_keys($categoryCountMap);
        $eventCountsByCategory = array_values($categoryCountMap);

        return $this->render('evenement/fire.html.twig', [
            'categoriesNames' => $categoriesNames,
            'eventCountsByCategory' => $eventCountsByCategory,
            'mostFamousEventName' => $this->getMostFamousEvent($allEvenements)
        ]);
    }
}