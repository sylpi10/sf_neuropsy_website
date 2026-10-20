<?php

namespace App\Twig;

use App\Entity\MenuItem;
use App\Repository\EditableTextRepository;
use App\Repository\MenuItemRepository;
use App\Repository\SitePageContentRepository;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    /** @var MenuItem[]|null */
    private ?array $menu = null;

    /** @var array<string, string|null>|null textes de la page en cours, indexés par clé */
    private ?array $texts = null;

    public function __construct(
        private readonly MenuItemRepository $menuItemRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly HtmlSanitizerInterface $appPageContent,
        private readonly SitePageContentRepository $sitePageContentRepository,
        private readonly EditableTextRepository $editableTextRepository,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('menu_items', [$this, 'getMenu']),
            new TwigFunction('menu_url', [$this, 'getUrl']),
            new TwigFunction('site_page_blocks', [$this, 'getSitePageBlocks']),
            new TwigFunction('editable_text', [$this, 'getEditableText'], ['is_safe' => ['html']]),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('page_html', [$this, 'sanitizePageHtml'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @return MenuItem[]
     */
    public function getMenu(): array
    {
        return $this->menu ??= $this->menuItemRepository->findMenu();
    }

    public function getUrl(MenuItem $item): ?string
    {
        $url = match ($item->getTargetType()) {
            MenuItem::TARGET_ROUTE => $item->getRouteName() ? $this->urlGenerator->generate($item->getRouteName()) : null,
            MenuItem::TARGET_PAGE => $item->getPage() ? $this->urlGenerator->generate('app_page', ['slug' => $item->getPage()->getSlug()]) : null,
            MenuItem::TARGET_URL => $item->getUrl(),
            default => null,
        };

        if ($url !== null && $item->getAnchor() && $item->getTargetType() !== MenuItem::TARGET_URL) {
            $url .= '#' . $item->getAnchor();
        }

        return $url;
    }

    /**
     * Sections ajoutées depuis l'admin à une page du site (SitePageContent), d'après sa route.
     *
     * @return \App\Entity\PageBlock[]
     */
    public function getSitePageBlocks(?string $routeName): array
    {
        if (!$routeName || !isset(MenuItem::SYSTEM_ROUTES[$routeName])) {
            return [];
        }

        return $this->sitePageContentRepository->findOneWithBlocks($routeName)?->getBlocks()->toArray() ?? [];
    }

    /**
     * Zone de texte modifiable dans l'admin (Textes des pages). Tous les textes de la page sont chargés
     * en une requête au premier appel. Une zone absente de la base n'affiche rien.
     */
    public function getEditableText(string $key): string
    {
        if ($this->texts === null) {
            $route = $this->requestStack->getMainRequest()?->attributes->get('_route');
            $this->texts = $route ? $this->editableTextRepository->findContentsByRoute($route) : [];
        }

        return $this->sanitizePageHtml($this->texts[$key] ?? null);
    }

    /**
     * HTML saisi dans l'éditeur (Trix) : nettoyé (balises / attributs autorisés dans
     * config/packages/html_sanitizer.yaml), et les titres <h1> de l'éditeur deviennent des <h3>
     * (le h1 de la page est son titre, l'intertitre de section est un h2).
     */
    public function sanitizePageHtml(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $html = preg_replace('#<(/?)h1(\s[^>]*)?>#i', '<$1h3>', $html);
        $html = $this->appPageContent->sanitize($html);

        // Trix écrit chaque paragraphe dans un <div> : on le remet en <p> pour retrouver les styles du site
        // (après nettoyage, les <div> n'ont plus aucun attribut), et on retire les paragraphes vides.
        $html = str_replace(['<div>', '</div>'], ['<p>', '</p>'], $html);

        return preg_replace('#<p>(\s|<br\s*/?>|&nbsp;)*</p>#i', '', $html);
    }
}
