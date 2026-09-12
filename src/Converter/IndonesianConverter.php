<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Converter;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class IndonesianConverter implements ConverterInterface
{
    private const WORDS = [
        '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas',
    ];

    /**
     * The largest integer PHP's default `precision` ini setting (14
     * significant digits) can round-trip through a string cast without
     * switching to scientific notation or losing digits. Anything above
     * this cannot be split into digits reliably by processNumber().
     */
    private const MAX_SUPPORTED_NUMBER = 99999999999999.0;

    /**
     * @var TranslatorInterface|null
     */
    private $translator;

    public function __construct(?TranslatorInterface $translator = null)
    {
        $this->translator = $translator;
    }

    public function convert(float $number, ?string $currency = null): string
    {
        $words = ucwords(strtolower($this->processNumber($number)));
        if ($number < 0) {
            $words = 'Minus ' . $words;
        }

        $currencyLabel = $this->translateCurrency($currency);

        return trim('' === $currencyLabel ? $words : $words . ' ' . $currencyLabel);
    }

    private function translateCurrency(?string $currency): string
    {
        if (null === $currency) {
            return '';
        }

        return null !== $this->translator ? $this->translator->trans($currency) : $currency;
    }

    protected function processNumber(float $number): string
    {
        if (abs($number) > self::MAX_SUPPORTED_NUMBER) {
            throw new \OverflowException('angka melebihi batas');
        }

        $numbers = explode('.', (string) $number);
        $front = 0 != $numbers[0] ? trim($this->terbilang((float) $numbers[0])) : 'nol';
        if (isset($numbers[1])) {
            $comma = trim($this->terbilang((float) $numbers[1]));

            return sprintf('%s koma %s', $front, $comma);
        }

        return $front;
    }

    protected function terbilang(float $number = 0): string
    {
        $number = abs($number);
        if ($number < 12) {
            return self::WORDS[(int) $number];
        }

        if ($number < 20) {
            return $this->terbilang($number - 10) . ' belas';
        }

        if ($number < 100) {
            return $this->terbilang(floor($number / 10)) . ' puluh ' . $this->terbilang(fmod($number, 10));
        }

        if ($number < 200) {
            return ' seratus ' . $this->terbilang($number - 100);
        }

        if ($number < 1000) {
            return $this->terbilang(floor($number / 100)) . ' ratus ' . $this->terbilang(fmod($number, 100));
        }

        if ($number < 2000) {
            return ' seribu ' . $this->terbilang($number - 1000);
        }

        if ($number < 1000000) {
            return $this->terbilang(floor($number / 1000)) . ' ribu ' . $this->terbilang(fmod($number, 1000));
        }

        if ($number < 1000000000) {
            return $this->terbilang(floor($number / 1000000)) . ' juta ' . $this->terbilang(fmod($number, 1000000));
        }

        if ($number < 1000000000000) {
            return $this->terbilang(floor($number / 1000000000))
                . ' milyar ' . $this->terbilang(fmod($number, 1000000000));
        }

        return $this->terbilang(floor($number / 1000000000000))
            . ' trilyun ' . $this->terbilang(fmod($number, 1000000000000));
    }
}
