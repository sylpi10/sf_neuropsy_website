<?php

namespace App\Twig;

use App\Entity\OpeningDay;
use App\Entity\SchedulePeriod;
use App\Repository\OpeningDayRepository;
use App\Repository\SchedulePeriodRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ScheduleExtension extends AbstractExtension
{
    // Une période est annoncée sur le site à partir de N jours avant son début
    private const ANNOUNCE_DAYS_AHEAD = 45;

    /** @var OpeningDay[]|null */
    private ?array $openDays = null;

    /** @var SchedulePeriod[]|null */
    private ?array $periods = null;

    public function __construct(
        private readonly OpeningDayRepository $openingDayRepository,
        private readonly SchedulePeriodRepository $schedulePeriodRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('open_days', [$this, 'getOpenDays']),
            new TwigFunction('schedule_periods', [$this, 'getPeriods']),
            new TwigFunction('banner_periods', [$this, 'getBannerPeriods']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('date_fr', [$this, 'formatDateFr']),
            new TwigFilter('join_fr', [$this, 'joinFr']),
        ];
    }

    /**
     * @return OpeningDay[]
     */
    public function getOpenDays(): array
    {
        return $this->openDays ??= $this->openingDayRepository->findOpenDays();
    }

    /**
     * @return SchedulePeriod[]
     */
    public function getPeriods(): array
    {
        return $this->periods ??= $this->schedulePeriodRepository->findUpcoming(
            new \DateTimeImmutable('today'),
            self::ANNOUNCE_DAYS_AHEAD,
        );
    }

    /**
     * @return SchedulePeriod[]
     */
    public function getBannerPeriods(): array
    {
        return array_values(array_filter(
            $this->getPeriods(),
            static fn (SchedulePeriod $period) => $period->isShowBanner(),
        ));
    }

    // "22 décembre 2026"
    public function formatDateFr(\DateTimeInterface $date): string
    {
        if (!class_exists(\IntlDateFormatter::class)) {
            return $date->format('d/m/Y');
        }

        return (new \IntlDateFormatter('fr_FR', \IntlDateFormatter::NONE, \IntlDateFormatter::NONE, null, null, 'd MMMM y'))
            ->format($date);
    }

    // ["lundi", "mardi", "jeudi"] => "lundi, mardi et jeudi"
    public function joinFr(array $items): string
    {
        $last = array_pop($items);

        return $items ? implode(', ', $items) . ' et ' . $last : (string) $last;
    }
}
