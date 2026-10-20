<?php

namespace App\Entity;

use App\Repository\PageRepository;
use App\Validator\NotReservedSlug;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Page créée depuis l'admin, servie sur /{slug} : bandeau + titre + sections (PageBlock).
 */
#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['slug'], message: 'Une autre page utilise déjà cette adresse.')]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: 'Uniquement des minuscules, chiffres et tirets (ex : ma-nouvelle-page).')]
    #[NotReservedSlug]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $heroImageName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 70, maxMessage: 'Google tronque les titres au-delà de ~60-70 caractères.')]
    private ?string $seoTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 180)]
    private ?string $seoDescription = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $published = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, PageBlock>
     */
    #[ORM\OneToMany(mappedBy: 'page', targetEntity: PageBlock::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[Assert\Valid]
    private Collection $blocks;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getHeroImageName(): ?string
    {
        return $this->heroImageName;
    }

    public function setHeroImageName(?string $heroImageName): static
    {
        $this->heroImageName = $heroImageName;

        return $this;
    }

    public function getSeoTitle(): ?string
    {
        return $this->seoTitle;
    }

    public function setSeoTitle(?string $seoTitle): static
    {
        $this->seoTitle = $seoTitle;

        return $this;
    }

    public function getSeoDescription(): ?string
    {
        return $this->seoDescription;
    }

    public function setSeoDescription(?string $seoDescription): static
    {
        $this->seoDescription = $seoDescription;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
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
            $block->setPage($this);
        }

        return $this;
    }

    public function removeBlock(PageBlock $block): static
    {
        if ($this->blocks->removeElement($block) && $block->getPage() === $this) {
            $block->setPage(null);
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->title;
    }
}
