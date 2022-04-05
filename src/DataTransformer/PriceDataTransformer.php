<?php

namespace Kematjaya\PriceBundle\DataTransformer;

use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Symfony\Component\Form\DataTransformerInterface;
/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class PriceDataTransformer implements DataTransformerInterface
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
    
    public function reverseTransform($value) 
    {   
        return ($value) ? $this->currencyFormat->priceToFloat($value):0;
    }

    public function transform($value) 
    {
        return ($value) ? $this->currencyFormat->priceToFloat($value) : 0;
    }
}
