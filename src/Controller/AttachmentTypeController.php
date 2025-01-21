<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AttachmentTypeController extends AbstractController
{
    #[Route('/collection/attachments')]
    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse([
            "AUTHENTICATED_DOCUMENT" => "Certificat d'authenticité",
            "PURCHASE_RECEIPT" => "Facture ou reçu d’achat",
            "PROVENANCE_HISTORY" => "Historique de provenance",
            "EXPERTISE_REPORT" => "Expertise et évaluation",
            "INSURANCE_EVALUATION" => "Évaluation d’assurance",
            "APPRAISAL_REPORT" => "Rapport d’estimation",
            "DESCRIPTIVE_SHEET" => "Fiche descriptive",
            "HIGH_RES_PHOTOS" => "Photographies haute résolution",
            "REFERENCE_CATALOG" => "Catalogue ou référence",
            "GRADING_CERTIFICATE" => "Certificat de grade",
            "RESTORATION_REPORT" => "Rapport de restauration",
            "CONSERVATION_GUIDE" => "Conseils de conservation",
            "CONDITION_CERTIFICATE" => "Certificat de condition",
            "LAB_ANALYSIS_REPORT" => "Rapport de laboratoire",
            "OWNERSHIP_TITLE" => "Titre de propriété",
            "DONATION_CERTIFICATE" => "Certificat de donation ou legs",
            "EXPORT_AUTHORIZATION" => "Autorisation de sortie du territoire",
            "OTHER" => "Autre",
        ]);
    }
}