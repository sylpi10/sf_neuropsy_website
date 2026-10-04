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
                    "Neuropsychologue La Bâtie-Neuve (05) – TCC | Emmanuelle Mir",
                "description" =>
                    "Neuropsychologue à La Bâtie-Neuve (05), près de Gap : bilans neuropsychologiques, TCC pour enfants, ados et adultes, formations professionnelles.",
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
                    "Le cabinet – Emmanuelle Mir, psychologue La Bâtie-Neuve",
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
                    "Honoraires et remboursements – Emmanuelle Mir",
                "description" =>
                    "Tarifs des consultations et des bilans neuropsychologiques, d'efficience et attentionnels. Paiement en plusieurs fois, remboursement par les mutuelles.",
            ],
        ]);
    }

    #[Route("/neuropsy", name: "app_neuropsy")]
    public function neuropsy(): Response
    {
        return $this->render("pages/neuropsy.html.twig", [
            "seo" => [
                "title" =>
                    "Bilans neuropsychologiques enfant, adulte | Emmanuelle Mir",
                "description" =>
                    "Bilans neuropsychologiques et psychométriques à La Bâtie-Neuve : pour qui, déroulement, durée, remédiation cognitive. Enfants, adultes et seniors.",
            ],
        ]);
    }

    #[Route("/tcc", name: "app_tcc")]
    public function tcc(): Response
    {
        return $this->render("pages/tcc.html.twig", [
            "seo" => [
                "title" =>
                    "Thérapies cognitives et comportementales | Emmanuelle Mir",
                "description" =>
                    "Thérapie cognitive et comportementale à La Bâtie-Neuve : anxiété, phobies, TOC, dépression, burn-out. Techniques utilisées et déroulement des séances.",
            ],
        ]);
    }

    #[Route("/references", name: "app_references")]
    public function references(): Response
    {
        return $this->render("pages/references.html.twig", [
            "seo" => [
                "title" =>
                    "Références et diplômes – Emmanuelle Mir, psychologue",
                "description" =>
                    "Parcours d'Emmanuelle Mir : psychologue clinicienne, psychothérapeute TCC, DU de neuropsychopathologie des apprentissages, EMDR, secteur médico-social.",
            ],
        ]);
    }

    #[Route("/mentions-legales", name: "app_mentions_legales")]
    public function mentionsLegales(): Response
    {
        return $this->render("pages/mentions_legales.html.twig", [
            "seo" => [
                "title" =>
                    "Mentions légales – Emmanuelle Mir, psychologue (05)",
                "description" =>
                    "Mentions légales du site d'Emmanuelle Mir, psychologue à La Bâtie-Neuve : éditeur, hébergeur, propriété intellectuelle et données personnelles.",
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
                    "Psycho-Parent-Alp – Formations parents et professionnels",
                "description" =>
                    "Psycho-Parent-Alp, organisme de formation certifié Qualiopi (05) : formations pour parents, aidants et professionnels du médico-social. Inscriptions.",
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
