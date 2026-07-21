<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Formation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            "aidant_et_famille" => "Aidants et familles",
            "professionnels" => "Professionnels",
        ];

        $categoryEntities = [];
        foreach ($categories as $code => $title) {
            $category = new Category();
            $category->setCode($code);
            $category->setTitle($title);
            $manager->persist($category);
            $categoryEntities[$code] = $category;
        }

        $formations = [
            [
                "category" => "aidant_et_famille",
                "title" =>
                    "Comprendre et soutenir le Trouble du spectre de l’autisme (TSA)",
                "duration" => 2,
                "startsAt" => "Septembre 2025",
                "priceInter" => "600€",
                "priceIntra" => null,
                "file" => "6_COMPRENDRE_ET_SOUTENIR_LENFANT_AVEC_TSA.pdf",
                "status" => true,
            ],
            [
                "category" => "aidant_et_famille",
                "title" =>
                    "Comprendre et soutenir le Trouble déficitaire de l' Attention avec ou sans Hyperactivité (TDA/H)",
                "duration" => 2,
                "startsAt" => "Octobre 2025",
                "priceInter" => "600€",
                "priceIntra" => null,
                "file" => "5_COMPRENDRE_ET_SOUTENIR_L'ENFANT_AVEC_UN_TDAH.pdf",
                "status" => true,
            ],
            [
                "category" => "aidant_et_famille",
                "title" =>
                    "S’approprier le Trouble des fonctions exécutives et l’accompagner au quotidien",
                "duration" => 2,
                "startsAt" => null,
                "priceInter" => null,
                "priceIntra" => null,
                "file" => null,
                "status" => false,
            ],
            [
                "category" => "aidant_et_famille",
                "title" => "Aider mon enfant à gérer ses émotions au quotidien",
                "duration" => 2,
                "startsAt" => null,
                "priceInter" => null,
                "priceIntra" => null,
                "file" => null,
                "status" => false,
            ],
            [
                "category" => "professionnels",
                "title" =>
                    "Psychopathologie, expressions comportementales et accompagnement (INTRA ETABLISSEMENT UNIQUEMENT)",
                "duration" => 4,
                "startsAt" => null,
                "priceInter" => null,
                "priceIntra" => "Nous consulter",
                "file" =>
                    "1_FORMATION_PSYCHOPATHOLOGIE_EXPRESSIONS_COMPORTEMENTALES_ET_ACCOMPAGNEMENT.pdf",
                "status" => true,
            ],
            [
                "category" => "professionnels",
                "title" =>
                    "Accueillir un enfant en situation de handicap ou suspicion de handicap",
                "duration" => 2,
                "startsAt" => null,
                "priceInter" => "620€ par stagiaire",
                "priceIntra" => "Nous consulter",
                "file" =>
                    "2_FORMATION_ACCUEILLIR_UN_ENFANT_EN_SITUATION_DE_HANDICAP_OU_SUSPICION_DE_HANDICAP.pdf",
                "status" => true,
            ],
            [
                "category" => "professionnels",
                "title" =>
                    "Comprendre et gérer les troubles du comportement en institution (INTRA ETABLISSEMENT UNIQUEMENT)",
                "duration" => 2,
                "startsAt" => null,
                "priceInter" => null,
                "priceIntra" => "Nous consulter",
                "file" =>
                    "3_COMPRENDRE_ET_GERER_LES_TROUBLES_DU_COMPORTEMENT_EN_INSTITUTION.pdf",
                "status" => true,
            ],
            [
                "category" => "professionnels",
                "title" =>
                    "Comprendre et gérer les troubles du comportement en structure petite enfance",
                "duration" => 2,
                "startsAt" => null,
                "priceInter" => "620€ par stagiaire",
                "priceIntra" => "Nous consulter",
                "file" =>
                    "4_COMPRENDRE_ET_GERER_AVEC_BIENTRAITANCE_LES%20COMPORTEMENTS_DITS%20AGRESSIFS_DU_JEUNE_ENFANT.pdf",
                "status" => true,
            ],
        ];

        $projectDir = dirname(__DIR__, 2);
        $assetsDir = $projectDir . "/public/assets/documents/";
        $uploadDir = $projectDir . "/public/uploads/formations/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        foreach ($formations as $row) {
            $formation = new Formation();
            $formation->setTitle($row["title"]);
            $formation->setDuration((float) $row["duration"]);
            $formation->setStartsAt($row["startsAt"]);
            $formation->setPriceInter($row["priceInter"]);
            $formation->setPriceIntra($row["priceIntra"]);
            $formation->setStatus((bool) $row["status"]);
            $formation->setCategory($categoryEntities[$row["category"]]);

            if ($row["file"]) {
                $source = $assetsDir . $row["file"];
                $target = $uploadDir . $row["file"];

                if (is_file($source) && !is_file($target)) {
                    @copy($source, $target);
                }

                $formation->setFileName($row["file"]);
            }

            $manager->persist($formation);
        }

        $manager->flush();
    }
}
