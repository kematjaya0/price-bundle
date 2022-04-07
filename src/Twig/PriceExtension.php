<?php

namespace Kematjaya\PriceBundle\Twig;

use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class PriceExtension extends AbstractExtension 
{
    
    /**
     * 
     * @var CurrencyFormatInterface
     */
    private $currencyFormat;

    public function __construct(CurrencyFormatInterface $currencyFormat)
    {
        $this->currencyFormat = $currencyFormat;
    }
    
    public function getFunctions()
    {
        return array(
            new TwigFunction('price', array($this, 'price'), array('is_safe' => array('html'))),
            new TwigFunction('price_symbol', function () {
                return $this->currencyFormat->getCurrencySymbol();
            }),
            new TwigFunction('thoisand_point', function () {
                return $this->currencyFormat->getThousandPoint();
            }),
            new TwigFunction('cent_point', function () {
                return $this->currencyFormat->getCentPoint();
            }),
            new TwigFunction('cent_limit', function () {
                return $this->currencyFormat->getCentLimit();
            })
        );
    }
    
    public function price($number = 0, int $centLimit = null, string $centPoint = null, string $thousandPoint = null)
    {
        if (!is_numeric($number)) {
            $number = 0;
        }
        
        return $this->currencyFormat->formatPrice($number, $centLimit, $centPoint, $thousandPoint);
    }
}
