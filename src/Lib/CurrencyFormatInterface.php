<?php

/**
 * This file is part of the kematjaya/price-bundle.
 */

namespace Kematjaya\PriceBundle\Lib;

/**
 * @package Kematjaya\PriceBundle\Lib
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface CurrencyFormatInterface 
{
    /**
     * Decode price string to float
     * @param string $price
     * @return float
     */
    public function priceToFloat(string $price = '0'):float;
    
    /**
     * Encode / formatting floating number to price format
     * @param float $number
     * @return string
     */
    public function formatPrice(float $number = 0, int $centLimit = null, string $centPoint = null, string $thousandPoint = null):string;
    
    public function getCurrencySymbol():?string;
}
