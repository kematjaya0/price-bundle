<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\DataTransformer;

use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class PriceDataTransformer implements DataTransformerInterface
{
    public function __construct(private readonly CurrencyFormatInterface $currencyFormat) {}

    /**
     * Model (float) to view (formatted string).
     */
    public function transform(mixed $value): ?string
    {
        return $value ? $this->currencyFormat->formatPrice((float) $value) : null;
    }

    /**
     * View (formatted string) to model (float).
     */
    public function reverseTransform(mixed $value): float
    {
        return $value ? $this->currencyFormat->priceToFloat((string) $value) : 0;
    }
}
