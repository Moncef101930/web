<?php
namespace App\Controller;

use App\Entity\evenement;
use App\Repository\evenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class StatController extends AbstractController
{
    #[Route('/statistique', name: 'app_evenement_statistiquess', methods: ['GET'])]
    public function statistiques(evenementRepository $evenementRepository): Response
    {
        // Récupérer tous les événements
        $evenements = $evenementRepository->findAll();

        // Tableau pour stocker les événements par lieu
        $eventsByLocation = [];

        // Organiser les événements par lieu et nom
        foreach ($evenements as $evenement) {
            $lieu = $evenement->getLieu();
            $nom = $evenement->getNom();

            if (!isset($eventsByLocation[$lieu])) {
                $eventsByLocation[$lieu] = [];
            }

            // Ajouter le nom de l'événement à la liste de ce lieu
            $eventsByLocation[$lieu][] = $nom;
        }

        // Préparer les données pour la vue
        $categoriesNames = array_keys($eventsByLocation);
        $eventCountsByCategory = array_map(fn($events) => count($events), $eventsByLocation);

        return $this->render('evenement/statistiques.html.twig', [
            'categoriesNames' => $categoriesNames,
            'eventCountsByCategory' => $eventCountsByCategory,
            'eventsByLocation' => $eventsByLocation, // Pour utiliser dans la vue
        ]);
    }
}
