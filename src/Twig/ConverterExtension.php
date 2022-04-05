<?php

namespace Kematjaya\PriceBundle\Twig;

use Kematjaya\PriceBundle\Converter\ConverterInterface;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ConverterExtension extends AbstractExtension
{
    /**
     *
     * @var ConverterInterface
     */
    private $converter;
    
    public function __construct(ConverterInterface $converter) 
    {
        $this->converter = $converter;
    }
    
    public function getFilters()
    {
        return [
            new TwigFilter('terbilang', [$this, 'getTerbilang']),
        ];
    }
    
    public function getTerbilang($number, bool $includeCurrency = false):string
    {
        return $this->converter->convert($number, $includeCurrency);
    }
}
