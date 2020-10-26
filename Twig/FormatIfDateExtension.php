<?php

namespace W3com\HulkBundle\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatIfDateExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('formatIfDate', [$this, 'formatPrice']),
        ];
    }

    public function formatPrice($value)
    {
        if (DateTime::createFromFormat('Y-m-d', $value) instanceof DateTime) {
            $date = DateTime::createFromFormat('Y-m-d', $value);

            return $date->format('d/m/Y');
        }

        return $value;
    }
}
