<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Icônes SVG inline (templates/icons/*.svg), à la place de la police Font Awesome.
 * Icônes : Font Awesome Free 5.15.4 (https://fontawesome.com), licence CC BY 4.0.
 */
class IconExtension extends AbstractExtension
{
    /** @var array<string, string> */
    private array $cache = [];

    public function __construct(
        #[Autowire('%kernel.project_dir%/templates/icons')]
        private readonly string $iconsDir,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [$this, 'icon'], ['is_safe' => ['html']]),
        ];
    }

    public function icon(string $name): string
    {
        return $this->cache[$name] ??= '<i class="icon" aria-hidden="true">'
            .trim(file_get_contents($this->iconsDir.'/'.basename($name).'.svg'))
            .'</i>';
    }
}
