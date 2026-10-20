<?php

namespace App\Entity;

use App\Repository\PageBlockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Section d'une Page admin, ou d'un contenu ajouté à une page du site (SitePageContent) :
 * intertitre, texte et image, tous optionnels.
 * Texte seul = bloc texte, image seule = bloc image, les deux = texte + image.
 */
#[ORM\Entity(repositoryClass: PageBlockRepository::class)]
class PageBlock
{
    public const IMAGE_RIGHT = 'right';
    public const IMAGE_LEFT = 'left';
    public const IMAGE_FULL = 'full';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'blocks')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Page $page = null;

    #[ORM\ManyToOne(inversedBy: 'blocks')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?SitePageContent $sitePageContent = null;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    // id HTML de la section, pour y descendre depuis le menu (page#ancre). Rempli d'après l'intertitre si vide.
    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Regex(pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: 'Uniquement des minuscules, chiffres et tirets (ex : emdr).')]
    private ?string $anchor = null;

    // HTML de l'éditeur, nettoyé à l'affichage (filtre page_html)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageWidth = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageHeight = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageAlt = null;

    #[ORM\Column(length: 10, options: ['default' => self::IMAGE_RIGHT])]
    #[Assert\Choice([self::IMAGE_RIGHT, self::IMAGE_LEFT, self::IMAGE_FULL])]
    private string $imagePosition = self::IMAGE_RIGHT;

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if (!$this->title && !trim(strip_tags((string) $this->content)) && !$this->imageName) {
            $context->buildViolation('Une section doit avoir au moins un intertitre, un texte ou une image.')
                ->atPath('title')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSitePageContent(): ?SitePageContent
    {
        return $this->sitePageContent;
    }

    public function setSitePageContent(?SitePageContent $sitePageContent): static
    {
        $this->sitePageContent = $sitePageContent;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
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

    public function getAnchor(): ?string
    {
        return $this->anchor;
    }

    public function setAnchor(?string $anchor): static
    {
        $this->anchor = $anchor;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageWidth(): ?int
    {
        return $this->imageWidth;
    }

    public function getImageHeight(): ?int
    {
        return $this->imageHeight;
    }

    public function setImageSize(?int $width, ?int $height): static
    {
        $this->imageWidth = $width;
        $this->imageHeight = $height;

        return $this;
    }

    public function getImageAlt(): ?string
    {
        return $this->imageAlt;
    }

    public function setImageAlt(?string $imageAlt): static
    {
        $this->imageAlt = $imageAlt;

        return $this;
    }

    public function getImagePosition(): string
    {
        return $this->imagePosition;
    }

    public function setImagePosition(string $imagePosition): static
    {
        $this->imagePosition = $imagePosition;

        return $this;
    }

    public function __toString(): string
    {
        return (string) ($this->title ?: 'Section');
    }
}
