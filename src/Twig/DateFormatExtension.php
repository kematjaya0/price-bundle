<?php

namespace Kematjaya\PriceBundle\Twig;

use Kematjaya\PriceBundle\Lib\AbstractDateFormat;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateFormatExtension extends AbstractExtension
{
    
    private AbstractDateFormat $dateFormat;
    
    public function __construct(AbstractDateFormat $dateFormat) 
    {
        $this->dateFormat = $dateFormat;
    }
    
    public function getFilters():array
    {
        return [
            new TwigFilter('date_format', [$this->dateFormat, 'format']),
            new TwigFilter('date_to_string', [$this->dateFormat, 'convertToString']),
        ];
    }
}
