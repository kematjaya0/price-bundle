<?php

/**
 * This file is part of the kematjaya/price-bundle.
 */

namespace Kematjaya\PriceBundle\Twig;

use Kematjaya\PriceBundle\Lib\AbstractDateFormat;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @package Kematjaya\PriceBundle\Twig
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DateFormatExtension extends AbstractExtension
{
    
    /**
     * 
     * @var AbstractDateFormat
     */
    private $dateFormat;
    
    public function __construct(AbstractDateFormat $dateFormat) 
    {
        $this->dateFormat = $dateFormat;
    }
    
    public function getFilters()
    {
        return [
            new TwigFilter('date_format', [$this->dateFormat, 'format']),
            new TwigFilter('date_to_string', [$this->dateFormat, 'convertToString']),
        ];
    }
}
