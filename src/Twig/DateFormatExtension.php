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
    /**
     * @var AbstractDateFormat
     */
    private $dateFormat;

    public function __construct(AbstractDateFormat $dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('date_format', [$this->dateFormat, 'format']),
            new TwigFilter('date_to_string', [$this->dateFormat, 'convertToString']),
        ];
    }
}