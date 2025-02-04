<?php

namespace Kematjaya\PriceBundle\Converter;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface ConverterInterface
{
    public function convert(float $number, string $currency = null):string;
}
