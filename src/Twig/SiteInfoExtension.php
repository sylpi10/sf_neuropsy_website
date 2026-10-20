<?php

namespace App\Twig;

use App\Repository\InformationRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SiteInfoExtension extends AbstractExtension
{
    private ?object $siteInfo = null;
    private bool $loaded = false;

    public function __construct(private readonly InformationRepository $informationRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('site_info', [$this, 'getSiteInfo']),
            new TwigFunction('show_qualiopi', [$this, 'showQualiopi']),
        ];
    }

    public function getSiteInfo(): ?object
    {
        if (!$this->loaded) {
            $this->siteInfo = $this->informationRepository->findFirst();
            $this->loaded = true;
        }

        return $this->siteInfo;
    }

    public function showQualiopi(): bool
    {
        return $this->getSiteInfo()?->isShowQualiopi() ?? false;
    }
}
