<?php

namespace App\Twig;

use App\Entity\MenuItem;
use App\Repository\MenuItemRepository;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    /** @var MenuItem[]|null */
    private ?array $menu = null;

    public function __construct(
        private readonly MenuItemRepository $menuItemRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly HtmlSanitizerInterface $appPageContent,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('menu_items', [$this, 'getMenu']),
            new TwigFunction('menu_url', [$this, 'getUrl']),
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

        return $this->appPageContent->sanitize($html);
    }
}
