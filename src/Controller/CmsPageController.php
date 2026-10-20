<?php

namespace App\Controller;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CmsPageController extends AbstractController
{
    // Priorité basse : les routes du site (/tcc, /cabinet, /backoffice...) passent toujours avant
    #[Route(
        "/{slug}",
        name: "app_page",
        requirements: ["slug" => "[a-z0-9]+(?:-[a-z0-9]+)*"],
        priority: -10,
    )]
    public function show(string $slug, PageRepository $pageRepository): Response
    {
        $page = $pageRepository->findOneBy(["slug" => $slug]);

        // Brouillon : visible uniquement par l'admin connecté (aperçu), jamais indexé
        $isPreview = $page && !$page->isPublished();
        if (!$page || ($isPreview && !$this->isGranted("ROLE_ADMIN"))) {
            throw $this->createNotFoundException();
        }

        $seo = [
            "title" => $page->getSeoTitle() ?: $page->getTitle() . " – Emmanuelle Mir",
            "description" => $page->getSeoDescription(),
        ];
        if ($page->getHeroImageName()) {
            $seo["image"] = "uploads/pages/" . $page->getHeroImageName();
        }
        if ($isPreview) {
            $seo["robots"] = "noindex, nofollow";
        }

        return $this->render("pages/cms_page.html.twig", [
            "page" => $page,
            "is_preview" => $isPreview,
            "seo" => array_filter($seo),
        ]);
    }
}
