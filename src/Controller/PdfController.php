<?php
// src/Controller/PdfController.php

namespace App\Controller;

use App\Service\PdfGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    private $pdfGeneratorService;

    // Injection du service PdfGeneratorService
    public function __construct(PdfGeneratorService $pdfGeneratorService)
    {
        $this->pdfGeneratorService = $pdfGeneratorService;
    }

    // Route pour générer et télécharger le PDF
    #[Route('/generate-pdf', name: 'generate_pdf')]
    public function generate(): Response
    {
        // Exemple de contenu HTML à convertir en PDF
        $htmlContent = "<h1>Mon Document PDF</h1><p>Ceci est un exemple de contenu HTML pour le PDF.</p>";

        try {
            // Appel du service pour générer le PDF
            $pdfPath = $this->pdfGeneratorService->generatePdfFromHtml($htmlContent);

            // Retourner le fichier PDF généré
            return $this->file($pdfPath);
        } catch (\Exception $e) {
            // Si une erreur se produit, afficher le message d'erreur
            return new Response('Erreur: ' . $e->getMessage());
        }
    }
}
