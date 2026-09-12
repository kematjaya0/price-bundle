<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Twig;

use Kematjaya\PriceBundle\Converter\ConverterInterface;
use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ConverterExtension extends AbstractExtension
{
    /**
     * @var ConverterInterface
     */
    private $converter;

    /**
     * @var CurrencyFormatInterface|null
     */
    private $currencyFormat;

    public function __construct(ConverterInterface $converter, ?CurrencyFormatInterface $currencyFormat = null)
    {
        $this->converter = $converter;
        $this->currencyFormat = $currencyFormat;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('terbilang', [$this, 'getTerbilang']),
        ];
    }

    public function getTerbilang(float $number, bool $includeCurrency = false, ?string $currency = null): string
    {
        if ($includeCurrency && null === $currency && null !== $this->currencyFormat) {
            $currency = $this->currencyFormat->getCurrency();
        }

        return $this->converter->convert($number, $currency);
    }
}
