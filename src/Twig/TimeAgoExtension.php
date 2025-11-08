<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeAgoExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_ago', [$this, 'timeAgo']),
        ];
    }

    public function timeAgo(?\DateTimeInterface $date): string
    {
        if (!$date) {
            return '';
        }

        $now  = new \DateTimeImmutable();
        $diff = $now->diff($date);

        if ($diff->y > 0) {
            $n = $diff->y;
            return 'il y a ' . $n . ' an' . ($n > 1 ? 's' : '');
        }

        if ($diff->m > 0) {
            $n = $diff->m;
            return 'il y a ' . $n . ' mois';
        }

        // ✅ ici on utilise ->days (le total des jours)
        if ($diff->days > 0) {
            $n = $diff->days;
            return 'il y a ' . $n . ' jour' . ($n > 1 ? 's' : '');
        }

        if ($diff->h > 0) {
            $n = $diff->h;
            return 'il y a ' . $n . ' heure' . ($n > 1 ? 's' : '');
        }

        if ($diff->i > 0) {
            $n = $diff->i;
            return 'il y a ' . $n . ' minute' . ($n > 1 ? 's' : '');
        }

        return 'à l’instant';
    }
}
