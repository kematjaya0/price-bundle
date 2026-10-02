<?php

/**
 * This file is part of the kematjaya/price-bundle.
 */

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Twig;

use Kematjaya\PriceBundle\Lib\AbstractDateFormat;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateFormatExtension extends AbstractExtension
{
    public function __construct(private readonly AbstractDateFormat $dateFormat) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('date_format', $this->dateFormat->format(...)),
            new TwigFilter('date_to_string', $this->dateFormat->convertToString(...)),
        ];
    }
}
