<?php

namespace App\EventListener;

use App\Entity\Page;
use App\Entity\PageBlock;
use App\Entity\SitePageContent;
use App\Entity\Slide;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Supprime du disque les images envoyées depuis l'admin quand leur entité est supprimée
 * (page, section retirée du formulaire, slide) : EasyAdmin ne le fait pas.
 */
#[AsEntityListener(event: Events::postRemove, method: 'removePageImages', entity: Page::class)]
#[AsEntityListener(event: Events::postRemove, method: 'removeBlockImage', entity: PageBlock::class)]
#[AsEntityListener(event: Events::postRemove, method: 'removeSitePageContentImages', entity: SitePageContent::class)]
#[AsEntityListener(event: Events::postRemove, method: 'removeSlideImages', entity: Slide::class)]
class UploadedImageCleanupListener
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/uploads')]
        private readonly string $uploadsDir,
    ) {
    }

    public function removePageImages(Page $page): void
    {
        $this->remove('pages', $page->getHeroImageName());

        // Sections supprimées en cascade par la base (ON DELETE CASCADE) : pas d'événement Doctrine pour elles
        foreach ($page->getBlocks() as $block) {
            $this->remove('pages', $block->getImageName());
        }
    }

    public function removeSitePageContentImages(SitePageContent $content): void
    {
        foreach ($content->getBlocks() as $block) {
            $this->remove('pages', $block->getImageName());
        }
    }

    public function removeBlockImage(PageBlock $block): void
    {
        $this->remove('pages', $block->getImageName());
    }

    public function removeSlideImages(Slide $slide): void
    {
        $this->remove('slides', $slide->getImageName());
        $this->remove('slides', $slide->getMobileImageName());
    }

    private function remove(string $folder, ?string $fileName): void
    {
        // basename() : on ne sort jamais du dossier d'upload, quel que soit le nom stocké
        if ($fileName && is_file($path = $this->uploadsDir . '/' . $folder . '/' . basename($fileName))) {
            @unlink($path);
        }
    }
}
