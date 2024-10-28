<?php

namespace Kematjaya\PriceBundle\Tests;

use Kematjaya\PriceBundle\Converter\IndonesianConverter;
use Kematjaya\PriceBundle\Lib\IndonesianDateFormat;
use Kematjaya\PriceBundle\Lib\AbstractDateFormat;
use PHPUnit\Framework\TestCase;

class IndonesianDateFormatTest extends TestCase
{
    public function testInstance(): AbstractDateFormat
    {
        $converter = new IndonesianConverter();
        $format = new IndonesianDateFormat($converter);
        
        $date = new \DateTime('2021-01-17 00:00:00');
        $this->assertEquals('17 Januari 2021 00:00:00', $format->format($date, 'd M Y H:i:s'));
        $expect = 'Hari Minggu Tanggal Tujuh Belas Bulan Januari Tahun Dua Ribu Dua Puluh Satu';
        $this->assertEquals($expect, $format->convertToString($date, 'D d M Y'));
        
        return $format;
    }
}
