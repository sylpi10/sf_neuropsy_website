<?php

namespace App\Entity;

use App\Repository\OpeningDayRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Semaine type du cabinet : une ligne par jour (1 = lundi ... 7 = dimanche).
 */
#[ORM\Entity(repositoryClass: OpeningDayRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_opening_day_day_of_week', columns: ['day_of_week'])]
class OpeningDay
{
    public const DAY_NAMES = [
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        7 => 'Dimanche',
    ];

    // Pour le JSON-LD (schema.org)
    public const SCHEMA_DAY_NAMES = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'smallint')]
    private int $dayOfWeek = 1;

    #[ORM\Column(name: 'is_open', options: ['default' => false])]
    private bool $open = false;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $hours = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayOfWeek(): int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getDayName(): string
    {
        return self::DAY_NAMES[$this->dayOfWeek] ?? '';
    }

    public function getSchemaDayName(): string
    {
        return self::SCHEMA_DAY_NAMES[$this->dayOfWeek] ?? '';
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function setOpen(bool $open): static
    {
        $this->open = $open;

        return $this;
    }

    public function getHours(): ?string
    {
        return $this->hours;
    }

    public function setHours(?string $hours): static
    {
        $this->hours = $hours;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getDayName();
    }
}
