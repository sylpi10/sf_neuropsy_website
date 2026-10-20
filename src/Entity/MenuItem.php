<?php

namespace App\Entity;

use App\Repository\MenuItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Entrée du menu principal. Un seul niveau de sous-menu : un élément qui a des enfants
 * devient un menu déroulant (son lien propre n'est alors pas utilisé).
 */
#[ORM\Entity(repositoryClass: MenuItemRepository::class)]
class MenuItem
{
    public const TARGET_ROUTE = 'route';
    public const TARGET_PAGE = 'page';
    public const TARGET_URL = 'url';
    public const TARGET_NONE = 'none';

    // Pages codées en Twig (PageController) proposées comme cible
    public const SYSTEM_ROUTES = [
        'app_home' => 'Accueil',
        'app_neuropsy' => 'Neuropsychologie',
        'app_tcc' => 'Les T.C.C',
        'app_honoraires' => 'Honoraires',
        'app_organisme_de_formation' => 'Se former (organisme de formation)',
        'app_references' => 'Références',
        'app_cabinet' => 'Le cabinet',
        'app_neuropsychologue_gap' => 'Neuropsychologue près de Gap',
        'app_mentions_legales' => 'Mentions légales',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $label = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    #[ORM\Column(options: ['default' => true])]
    private bool $visible = true;

    // Mise en avant visuelle (couleur terracotta, ex : "Se former")
    #[ORM\Column(options: ['default' => false])]
    private bool $highlighted = false;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    private Collection $children;

    #[ORM\Column(length: 10, options: ['default' => self::TARGET_ROUTE])]
    #[Assert\Choice([self::TARGET_ROUTE, self::TARGET_PAGE, self::TARGET_URL, self::TARGET_NONE])]
    private string $targetType = self::TARGET_ROUTE;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $routeName = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Page $page = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Regex(pattern: '/^[^#\s]+$/u', message: "L'ancre s'écrit sans # ni espace (ex : bilans).")]
    private ?string $anchor = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(requireTld: true)]
    private ?string $url = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->parent && ($this->parent === $this || $this->parent->getParent())) {
            $context->buildViolation('Un seul niveau de sous-menu : choisissez un élément principal comme parent.')
                ->atPath('parent')->addViolation();
        }
        if ($this->parent && !$this->children->isEmpty()) {
            $context->buildViolation('Cet élément a déjà des sous-éléments, il ne peut pas être lui-même dans un sous-menu.')
                ->atPath('parent')->addViolation();
        }

        if ($this->targetType === self::TARGET_ROUTE && !isset(self::SYSTEM_ROUTES[$this->routeName ?? ''])) {
            $context->buildViolation('Choisissez la page du site.')->atPath('routeName')->addViolation();
        }
        if ($this->targetType === self::TARGET_PAGE && !$this->page) {
            $context->buildViolation("Choisissez la page créée dans l'admin.")->atPath('page')->addViolation();
        }
        if ($this->targetType === self::TARGET_URL && !$this->url) {
            $context->buildViolation("Indiquez l'adresse du lien.")->atPath('url')->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function isHighlighted(): bool
    {
        return $this->highlighted;
    }

    public function setHighlighted(bool $highlighted): static
    {
        $this->highlighted = $highlighted;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @return self[]
     */
    public function getVisibleChildren(): array
    {
        return array_values($this->children->filter(static fn (self $child) => $child->isVisible())->toArray());
    }

    public function getTargetType(): string
    {
        return $this->targetType;
    }

    public function setTargetType(string $targetType): static
    {
        $this->targetType = $targetType;

        return $this;
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

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getAnchor(): ?string
    {
        return $this->anchor;
    }

    public function setAnchor(?string $anchor): static
    {
        $this->anchor = $anchor !== null ? ltrim(trim($anchor), '#') : null;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->label;
    }
}
