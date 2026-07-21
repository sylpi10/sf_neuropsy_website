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
            "title" =>
                "Neuropsychologue à La Bâtie-Neuve (05) – TCC, bilans & formations | Emanuelle Mir",
            "meta_description" =>
                "Neuropsychologue à La Bâtie-Neuve (Hautes-Alpes 05), proche Briançon & Gap : bilans neuropsychologiques, thérapies TCC, accompagnement adultes/enfants/ados. Formations et ateliers pour professionnels. Rendez-vous au cabinet.",
            "meta_name" =>
                "neuropsychologue La Bâtie-Neuve, neuropsychologue 05, TCC Hautes-Alpes, bilan neuropsychologique Briançon, neuropsychologie Gap, thérapie cognitive et comportementale 05, troubles attention, mémoire, apprentissages, formations neuropsychologie, ateliers professionnels, Emanuelle Mir",
            "meta_robots" => "index,follow",
            "canonical" => "https://psytcc-hautesalpes.fr/",
            "og" => [
                "title" =>
                    "Emanuelle Mir – Neuropsychologue à La Bâtie-Neuve (05) | TCC, bilans & formations",
                "description" =>
                    "Cabinet de neuropsychologie à La Bâtie-Neuve, proche Briançon & Gap : bilans, TCC, accompagnement et formations.",
                "type" => "website",
                "url" => "https://psytcc-hautesalpes.fr/",
                "image" =>
                    "https://psytcc-hautesalpes.fr/assets/images/logo.png",
                "site_name" =>
                    "Emanuelle Mir – Neuropsychologue à La Bâtie-Neuve (05) | TCC, bilans & formations",
                "locale" => "fr_FR",
            ],
        ]);
    }

    #[Route("/cabinet", name: "app_cabinet")]
    public function cabinet(): Response
    {
        return $this->render("pages/cabinet.html.twig", [
            "title" => "Le cabinet",
        ]);
    }

    #[Route("/honoraires", name: "app_honoraires")]
    public function honoraires(): Response
    {
        return $this->render("pages/honoraires.html.twig", [
            "title" => "Honoraires",
        ]);
    }

    #[Route("/neuropsy", name: "app_neuropsy")]
    public function neuropsy(): Response
    {
        return $this->render("pages/neuropsy.html.twig", [
            "title" => "La Neuropsy",
        ]);
    }

    #[Route("/tcc", name: "app_tcc")]
    public function tcc(): Response
    {
        return $this->render("pages/tcc.html.twig", [
            "title" => "Les TCC",
        ]);
    }

    #[Route("/references", name: "app_references")]
    public function references(): Response
    {
        return $this->render("pages/references.html.twig", [
            "title" => "Références",
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
            "title" => "Les formations",
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
