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
    /**
     * @var CurrencyFormatInterface
     */
    private $currencyFormat;

    public function __construct(CurrencyFormatInterface $currencyFormat)
    {
        $this->currencyFormat = $currencyFormat;
    }

    /**
     * Model (float) to view (formatted string).
     *
     * @param mixed $value
     */
    public function transform($value): ?string
    {
        return $value ? $this->currencyFormat->formatPrice((float) $value) : null;
    }

    /**
     * View (formatted string) to model (float).
     *
     * @param mixed $value
     */
    public function reverseTransform($value): float
    {
        return $value ? $this->currencyFormat->priceToFloat((string) $value) : 0;
    }
}
