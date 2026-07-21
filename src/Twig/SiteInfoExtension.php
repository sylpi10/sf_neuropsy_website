<?php

namespace App\Twig;

use App\Repository\InformationRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SiteInfoExtension extends AbstractExtension
{
    public function __construct(private readonly InformationRepository $informationRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('site_info', [$this, 'getSiteInfo']),
        ];
    }

    public function getSiteInfo(): ?object
    {
        return $this->informationRepository->findFirst();
    }
}
