<?php

/**
 * This file is part of the kematjaya/price-bundle.
 */

namespace Kematjaya\PriceBundle\Lib;

use Kematjaya\PriceBundle\Converter\ConverterInterface;
use DateTimeInterface;

/**
 * @package Kematjaya\Currency\Lib
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
abstract class AbstractDateFormat implements DateFormatInterface
{
    
    /**
     * 
     * @var ConverterInterface
     */
    protected $converter;
    
    function __construct(ConverterInterface $converter) 
    {
        $this->converter = $converter;
    }
    
    abstract public function getDayName(string $day):string;
    
    abstract public function getMonthName(string $month):string;
    
    abstract public function reverse(string $date, string $splitTime = ','):?\DateTimeInterface;
    
    public function format(DateTimeInterface $date, $format = 'd M Y')
    {
        $prefix = $this->getPrefix($format);
        if (is_null($prefix)) {
            
            return $date->format($format);
        }
        
        $result = $this->doFormat($date, $format, $prefix);
        
        return implode($prefix, $result);
    }
    
    protected function doFormat(DateTimeInterface $date, string $format, string $prefix):array
    {
        $formatArr = explode($prefix, $format);
        $result = [];
        foreach ($formatArr as $value) {
            switch ($value) {
                case 'D':
                    $result[$value] = $this->getDayName($date->format('D'));
                    break;
                case 'M':
                    $result[$value] = $this->getMonthName($date->format('M'));
                    break;
                default:
                    $result[$value] = $date->format($value);
                    break;
            }
        }
        
        return $result;
    }
    
    abstract public function getLabels():array;
    
    /**
     * Convert to String
     * @param DateTimeInterface $date
     * @param string $format
     * @return type
     */
    public function convertToString(DateTimeInterface $date, string $format = 'd M Y')
    {
        $prefix = $this->getPrefix($format);
        if (is_null($prefix)) {
            
            return $date->format($format);
        }
        
        $labels = $this->getLabels();
        $formats = $this->doFormat($date, $format, $prefix);
        foreach ($formats as $k => $v) {
            if (!is_numeric($v)) {
                continue;
            }
            
            $formats[$k] = $this->converter->convert((int) $v);
        }
        
        foreach ($formats as $k => $v) {
            $keteranganWaktu = (isset($labels[$k])) ? $labels[$k] : '';
            $formats[$k] = sprintf("%s %s", trim($keteranganWaktu), trim($v));
        }
        
        return implode($prefix, $formats);
    }
    
    protected function getPrefix(string $format):?string
    {
        $prefix = $this->prefix();
        $prev = null;
        foreach ($prefix as $v) {
            if (false === strpos($format, $v)) {
                continue;
            }
            
            $prev = $v;
        }
        
        return $prev;
    }
    
    protected function prefix()
    {
        return ['/', '-', ' '];
    }
}
