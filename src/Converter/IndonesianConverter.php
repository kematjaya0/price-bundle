<?php

namespace Kematjaya\PriceBundle\Converter;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class IndonesianConverter implements ConverterInterface
{
    
    public function convert(float $number, bool $includeCurrency = false): string 
    {
        $result = trim($this->terbilang($number));
        if ($number < 0) {
            $result = "minus ". trim($this->terbilang($number));
        }
        
        return ($includeCurrency) ? trim(ucwords(strtolower($result)). " Rupiah") : trim(ucwords(strtolower($result)));
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
