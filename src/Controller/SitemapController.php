<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SitemapController extends AbstractController
{
    private const PAGES = [
        "app_home" => "1.0",
        "app_neuropsy" => "0.8",
        "app_tcc" => "0.8",
        "app_cabinet" => "0.8",
        "app_honoraires" => "0.6",
        "app_organisme_de_formation" => "0.6",
        "app_references" => "0.5",
    ];

    #[Route("/sitemap.xml", name: "app_sitemap", format: "xml")]
    public function sitemap(): Response
    {
        $urls = [];
        foreach (self::PAGES as $route => $priority) {
            $urls[] = [
                "path" => $this->generateUrl($route),
                "priority" => $priority,
            ];
        }

        $response = $this->render("sitemap.xml.twig", ["urls" => $urls]);
        $response->headers->set("Content-Type", "application/xml");

        return $response;
    }
}
