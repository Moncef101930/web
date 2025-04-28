<?php
// src/Service/PdfGeneratorService.php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGeneratorService
{
    private $dompdf;

    public function __construct()
    {
        // Configuration des options DOMPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        // Instantiation de DOMPDF avec les options
        $this->dompdf = new Dompdf($options);
    }

    public function generatePdfFromHtml(string $htmlContent): string
    {
        // Charge le contenu HTML dans DOMPDF
        $this->dompdf->loadHtml($htmlContent);

        // (Facultatif) Définir le format de la page (A4, Lettre, etc.)
        $this->dompdf->setPaper('A4');

        // Rendre le PDF à partir du contenu HTML
        $this->dompdf->render();

        // Générer le PDF dans un fichier temporaire et renvoyer l'URL du fichier
        $output = $this->dompdf->output();

        // Sauvegarder le fichier généré dans le dossier public (ou un autre répertoire de ton choix)
        $pdfPath = 'uploads/pdf/generated_document.pdf';
        file_put_contents($pdfPath, $output);

        return $pdfPath; // Retourne le chemin vers le fichier PDF généré
    }
}
