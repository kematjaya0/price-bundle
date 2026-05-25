<?php

namespace Kematjaya\PriceBundle\Lib;

/**
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface CurrencyFormatInterface
{
    public function priceToFloat(string $price = '0', ?string $currency = null, ?int $centLimit = null, ?string $centPoint = null, ?string $thousandPoint = null): float;

    public function formatPrice(float $number = 0, ?int $centLimit = null, ?string $centPoint = null, ?string $thousandPoint = null, ?string $currency = null): string;

    public function getCurrencySymbol(): ?string;

    public function getCentLimit(): int;

    public function getCentPoint(): string;

    public function getThousandPoint(): string;

    public function getCurrency(): string;
}
