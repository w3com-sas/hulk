<?php

namespace W3com\HulkBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ExplodeSpaceExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter(
                'explodeSpace',
                [$this, 'explodeSpace'],
                ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }

    public function explodeSpace($value)
    {
        return str_replace(' ', ' <i class="fad fa-plus-circle text-white"></i> ', $value);
    }
}
