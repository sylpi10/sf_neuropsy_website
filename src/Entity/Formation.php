<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Category;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?float $duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $startsAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $priceIntra = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $priceInter = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column(options: ["default" => true])]
    private bool $status = true;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getStartsAt(): ?string
    {
        return $this->startsAt;
    }

    public function setStartsAt(?string $startsAt): static
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getPriceIntra(): ?string
    {
        return $this->priceIntra;
    }

    public function setPriceIntra(?string $priceIntra): static
    {
        $this->priceIntra = $priceIntra;

        return $this;
    }

    public function getPriceInter(): ?string
    {
        return $this->priceInter;
    }

    public function setPriceInter(?string $priceInter): static
    {
        $this->priceInter = $priceInter;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
