<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Lib;

/**
 * @package Kematjaya\PriceBundle\Lib
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface CurrencyFormatInterface
{
    public function priceToFloat(
        string $price = '0',
        ?string $currency = null,
        ?int $centLimit = null,
        ?string $centPoint = null,
        ?string $thousandPoint = null
    ): float;

    public function formatPrice(
        float $number = 0,
        ?int $centLimit = null,
        ?string $centPoint = null,
        ?string $thousandPoint = null,
        ?string $currency = null
    ): string;

    public function getCurrencySymbol(): ?string;

    public function getCentLimit(): int;

    public function setCentLimit(int $centLimit): self;

    /**
     * Cent limit for a specific currency code, falling back to getCentLimit()
     * when that currency has no override configured.
     */
    public function getCentLimitByCurrency(string $currency): int;

    /**
     * @return array<string, int> currency code => cent limit overrides
     */
    public function getCentLimits(): array;

    public function getCentPoint(): string;

    public function getThousandPoint(): string;

    public function getCurrency(): string;

    public function setCurrency(string $currency): self;
}