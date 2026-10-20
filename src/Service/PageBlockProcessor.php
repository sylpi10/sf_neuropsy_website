<?php

namespace App\Service;

use App\Entity\PageBlock;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Préparation des sections avant enregistrement (pages admin et contenus ajoutés aux pages du site) :
 * ancre déduite de l'intertitre, images converties / réduites, dimensions stockées, ordre renuméroté.
 */
class PageBlockProcessor
{
    private const IMAGE_MAX_WIDTH = 1200;

    public function __construct(
        private readonly ImageOptimizer $imageOptimizer,
        #[Autowire('%kernel.project_dir%/public/uploads/pages')]
        private readonly string $uploadDir,
    ) {
    }

    /**
     * @param iterable<PageBlock> $blocks
     */
    public function process(iterable $blocks): void
    {
        $blocks = is_array($blocks) ? array_values($blocks) : array_values(iterator_to_array($blocks));

        foreach ($blocks as $block) {
            if (!$block->getAnchor() && $block->getTitle()) {
                $block->setAnchor(substr((new AsciiSlugger('fr'))->slug($block->getTitle())->lower()->toString(), 0, 100));
            }

            if (!$block->getImageName()) {
                $block->setImageSize(null, null);
                continue;
            }

            $block->setImageName(
                $this->imageOptimizer->optimize($this->uploadDir, $block->getImageName(), self::IMAGE_MAX_WIDTH),
            );
            // Dimensions stockées pour les attributs width/height (pas de décalage de mise en page au chargement)
            $size = @getimagesize($this->uploadDir . '/' . $block->getImageName());
            $block->setImageSize($size[0] ?? null, $size[1] ?? null);
        }

        $this->renumber($blocks);
    }

    /**
     * Renumérote 10, 20, 30... : les sections sans ordre vont à la fin, dans l'ordre du formulaire.
     *
     * @param PageBlock[] $blocks
     */
    private function renumber(array $blocks): void
    {
        $indexed = array_map(null, $blocks, array_keys($blocks));

        usort($indexed, static function (array $a, array $b): int {
            [$blockA, $indexA] = $a;
            [$blockB, $indexB] = $b;

            return [$blockA->getPosition() ?? PHP_INT_MAX, $indexA]
                <=> [$blockB->getPosition() ?? PHP_INT_MAX, $indexB];
        });

        foreach ($indexed as $i => [$block]) {
            $block->setPosition(($i + 1) * 10);
        }
    }
}
