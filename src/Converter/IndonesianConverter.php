<?php

namespace Kematjaya\PriceBundle\Converter;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class IndonesianConverter implements ConverterInterface
{
    private TranslatorInterface $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function convert(float $number, string $currency = null): string
    {
        $result = $this->processNumber($number);
        $currencyTrans = null !== $currency ? $this->translator->trans($currency) : null;
        if ($number < 0) {
            $result = "minus ". ucwords(strtolower($result)) . " " . $currencyTrans;
        }

        return trim(ucwords(strtolower($result)). " " . $currencyTrans);
    }

    protected function processNumber(float $number):string
    {
        $numbers = explode(".", $number);
        $front = 0 != $numbers[0] ? trim($this->terbilang($numbers[0])) : "nol";
        if (isset($numbers[1]) && null !== $numbers[1])  {
            $comma = trim($this->terbilang($numbers[1]));
            return sprintf("%s koma %s", $front, $comma);
        }

        return $front;
    }

    protected function terbilang(float $number = 0):string
    {
        $number = abs($number);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        if ($number < 12) {
            return $huruf[$number];
        }

        if ($number < 20) {
            return $this->terbilang($number - 10). " belas";
        }

        if ($number < 100) {
            return $this->terbilang($number/10)." puluh ". $this->terbilang($number % 10);
        }

        if ($number < 200) {
            return " seratus " . $this->terbilang($number - 100);
        }

        if ($number < 1000) {
            return $this->terbilang($number/100) . " ratus " . $this->terbilang($number % 100);
        }

        if ($number < 2000) {
            return " seribu " . $this->terbilang($number - 1000);
        }

        if ($number < 1000000) {
            return $this->terbilang($number/1000) . " ribu " . $this->terbilang($number % 1000);
        }

        if ($number < 1000000000) {
            return $this->terbilang($number/1000000) . " juta " . $this->terbilang($number % 1000000);
        }

        if ($number < 1000000000000) {
            return $this->terbilang($number/1000000000) . " milyar " . $this->terbilang(fmod($number,1000000000));
        }

        if ($number < 1000000000000000) {
            return $this->terbilang($number/1000000000000) . " trilyun " . $this->terbilang(fmod($number,1000000000000));
        }

        throw new \Exception("angka melebihi batas");
    }
}
