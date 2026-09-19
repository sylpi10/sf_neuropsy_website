<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route("/", name: "app_home")]
    public function home(): Response
    {
        return $this->render("pages/index.html.twig", [
            "seo" => [
                "title" =>
                    "Neuropsychologue à La Bâtie-Neuve (05) – TCC, bilans & formations | Emmanuelle Mir",
                "description" =>
                    "Neuropsychologue à La Bâtie-Neuve (Hautes-Alpes 05), proche Briançon & Gap : bilans neuropsychologiques, thérapies TCC, accompagnement adultes/enfants/ados. Formations et ateliers pour professionnels. Rendez-vous au cabinet.",
                "keywords" =>
                    "neuropsychologue La Bâtie-Neuve, neuropsychologue 05, TCC Hautes-Alpes, bilan neuropsychologique Briançon, neuropsychologie Gap, thérapie cognitive et comportementale 05, troubles attention, mémoire, apprentissages, formations neuropsychologie, ateliers professionnels, Emmanuelle Mir",
                "og_title" =>
                    "Emmanuelle Mir – Neuropsychologue à La Bâtie-Neuve (05) | TCC, bilans & formations",
                "og_description" =>
                    "Cabinet de neuropsychologie à La Bâtie-Neuve, proche Briançon & Gap : bilans, TCC, accompagnement et formations.",
            ],
        ]);
    }

    #[Route("/cabinet", name: "app_cabinet")]
    public function cabinet(): Response
    {
        return $this->render("pages/cabinet.html.twig", [
            "seo" => [
                "title" =>
                    "Le cabinet – Emmanuelle Mir, psychologue à La Bâtie-Neuve (05)",
                "description" =>
                    "Cabinet de psychologie à la Maison de santé de La Bâtie-Neuve (Hautes-Alpes) : adresse, jours de consultation et prise de rendez-vous avec Emmanuelle Mir.",
            ],
        ]);
    }

    #[Route("/honoraires", name: "app_honoraires")]
    public function honoraires(): Response
    {
        return $this->render("pages/honoraires.html.twig", [
            "seo" => [
                "title" =>
                    "Honoraires et remboursements – Emmanuelle Mir, psychologue (05)",
                "description" =>
                    "Tarifs des consultations et des bilans (neuropsychologique, efficience intellectuelle, attentionnel), paiement en plusieurs fois et informations sur le remboursement par les mutuelles.",
            ],
        ]);
    }

    #[Route("/neuropsy", name: "app_neuropsy")]
    public function neuropsy(): Response
    {
        return $this->render("pages/neuropsy.html.twig", [
            "seo" => [
                "title" =>
                    "Évaluations neuropsychologiques, bilans enfants et adultes | Emmanuelle Mir",
                "description" =>
                    "Bilans neuropsychologiques et psychométriques à La Bâtie-Neuve : pour qui, déroulement, durée et remédiation cognitive. Enfants, adultes et personnes âgées.",
            ],
        ]);
    }

    #[Route("/tcc", name: "app_tcc")]
    public function tcc(): Response
    {
        return $this->render("pages/tcc.html.twig", [
            "seo" => [
                "title" =>
                    "Thérapies cognitives et comportementales (TCC) | Emmanuelle Mir",
                "description" =>
                    "Thérapie cognitive et comportementale à La Bâtie-Neuve : techniques utilisées, troubles pris en charge (anxiété, phobies, TOC, dépression, burn-out…) et déroulement d'une thérapie.",
            ],
        ]);
    }

    #[Route("/references", name: "app_references")]
    public function references(): Response
    {
        return $this->render("pages/references.html.twig", [
            "seo" => [
                "title" =>
                    "Références et diplômes – Emmanuelle Mir, psychologue clinicienne",
                "description" =>
                    "Parcours d'Emmanuelle Mir : psychologue clinicienne, psychothérapeute TCC, DU de neuropsychopathologie des apprentissages, EMDR et expériences en secteur médico-social.",
            ],
        ]);
    }

    #[Route("/organisme-de-formation", name: "app_organisme_de_formation")]
    public function organismeDeFormation(
        FormationRepository $formationRepository,
        CategoryRepository $categoryRepository,
    ): Response {
        $aidantCategory = $categoryRepository->findOneBy([
            "code" => "aidant_et_famille",
        ]);
        $proCategory = $categoryRepository->findOneBy([
            "code" => "professionnels",
        ]);

        return $this->render("pages/organisme_de_formation.html.twig", [
            "seo" => [
                "title" =>
                    "Organisme de formation Psycho-Parent-Alp – formations parents et professionnels",
                "description" =>
                    "Psycho-Parent-Alp, organisme de formation certifié Qualiopi (Hautes-Alpes) : formations pour parents, aidants et professionnels du médico-social, inscriptions et informations pratiques.",
            ],
            "aidantCategory" => $aidantCategory,
            "proCategory" => $proCategory,
            "aidantFormations" => $formationRepository->findByCategoryCode(
                "aidant_et_famille",
            ),
            "proFormations" => $formationRepository->findByCategoryCode(
                "professionnels",
            ),
        ]);
    }
}
