<?php

namespace App\Entity;

use App\Repository\SitePageContentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sections ajoutées depuis l'admin à la fin d'une page codée en Twig (TCC, Cabinet...).
 * Affichées par base.html.twig, d'après la route de la page.
 */
#[ORM\Entity(repositoryClass: SitePageContentRepository::class)]
#[UniqueEntity(fields: ['routeName'], message: 'Cette page a déjà son contenu ajouté : modifiez-le plutôt que d\'en créer un second.')]
class SitePageContent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [self::class, 'getRouteNames'])]
    private ?string $routeName = null;

    /**
     * @var Collection<int, PageBlock>
     */
    #[ORM\OneToMany(mappedBy: 'sitePageContent', targetEntity: PageBlock::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[Assert\Valid]
    private Collection $blocks;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
    }

    /**
     * @return string[]
     */
    public static function getRouteNames(): array
    {
        return array_keys(MenuItem::SYSTEM_ROUTES);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function setRouteName(?string $routeName): static
    {
        $this->routeName = $routeName;

        return $this;
    }

    public function getPageLabel(): string
    {
        return MenuItem::SYSTEM_ROUTES[$this->routeName] ?? (string) $this->routeName;
    }

    /**
     * @return Collection<int, PageBlock>
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    public function addBlock(PageBlock $block): static
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks->add($block);
            $block->setSitePageContent($this);
        }

        return $this;
    }

    public function removeBlock(PageBlock $block): static
    {
        if ($this->blocks->removeElement($block) && $block->getSitePageContent() === $this) {
            $block->setSitePageContent(null);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getPageLabel();
    }
}
