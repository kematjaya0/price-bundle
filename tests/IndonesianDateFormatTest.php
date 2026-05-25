<?php

namespace Kematjaya\PriceBundle\Tests;

use Kematjaya\PriceBundle\Converter\IndonesianConverter;
use Kematjaya\PriceBundle\Lib\AbstractDateFormat;
use Kematjaya\PriceBundle\Lib\IndonesianDateFormat;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndonesianDateFormatTest extends TestCase
{
    private function createTranslator(): TranslatorInterface
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return $translator;
    }

    public function testInstance(): AbstractDateFormat
    {
        $converter = new IndonesianConverter($this->createTranslator());
        $format = new IndonesianDateFormat($converter);

        $date = new \DateTime('2021-01-17 00:00:00');
        $this->assertEquals('17 Januari 2021 00:00:00', $format->format($date, 'd M Y H:i:s'));
        $expect = 'Hari Minggu Tanggal Tujuh Belas Bulan Januari Tahun Dua Ribu Dua Puluh Satu';
        $this->assertEquals($expect, $format->convertToString($date, 'D d M Y'));

        return $format;
    }

    public function testFormatWithSlash(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());
        $format = new IndonesianDateFormat($converter);

        $date = new \DateTime('2021-01-17');
        $this->assertEquals('17/Januari/2021', $format->format($date, 'd/M/Y'));
    }

    public function testFormatWithDash(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());
        $format = new IndonesianDateFormat($converter);

        $date = new \DateTime('2021-01-17');
        $this->assertEquals('17-Januari-2021', $format->format($date, 'd-M-Y'));
    }

    public function testConvertToStringLargeNumber(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());
        $format = new IndonesianDateFormat($converter);

        $date = new \DateTime('2025-12-25');
        $result = $format->convertToString($date, 'd M Y');
        $this->assertStringContainsString('Tanggal', $result);
        $this->assertStringContainsString('Bulan', $result);
        $this->assertStringContainsString('Tahun', $result);
    }

    public function testReverseDate(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());
        $format = new IndonesianDateFormat($converter);

        $result = $format->reverse('17 Januari 2021,12:09');
        $this->assertInstanceOf(\DateTimeInterface::class, $result);
        $this->assertEquals('2021-01-17 12:09', $result->format('Y-m-d H:i'));
    }

    public function testReverseDateWithoutTime(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());
        $format = new IndonesianDateFormat($converter);

        $result = $format->reverse('05 Juli 2018');
        $this->assertInstanceOf(\DateTimeInterface::class, $result);
        $this->assertEquals('2018-07-05', $result->format('Y-m-d'));
    }

    public function testReverseInvalidDate(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());
        $format = new IndonesianDateFormat($converter);

        $this->assertNull($format->reverse(''));
        $this->assertNull($format->reverse('not a date'));
    }

    public function testGetDayName(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());
        $format = new IndonesianDateFormat($converter);

        $this->assertEquals('Senin', $format->getDayName('Mon'));
        $this->assertEquals('Selasa', $format->getDayName('Tue'));
        $this->assertEquals('Rabu', $format->getDayName('Wed'));
        $this->assertEquals('Kamis', $format->getDayName('Thu'));
        $this->assertEquals('Jumat', $format->getDayName('Fri'));
        $this->assertEquals('Sabtu', $format->getDayName('Sat'));
        $this->assertEquals('Minggu', $format->getDayName('Sun'));
        $this->assertEquals('Foo', $format->getDayName('Foo'));
    }

    public function testGetMonthName(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());
        $format = new IndonesianDateFormat($converter);

        $this->assertEquals('Januari', $format->getMonthName('Jan'));
        $this->assertEquals('Februari', $format->getMonthName('Feb'));
        $this->assertEquals('Maret', $format->getMonthName('Mar'));
        $this->assertEquals('April', $format->getMonthName('Apr'));
        $this->assertEquals('Mei', $format->getMonthName('May'));
        $this->assertEquals('Juni', $format->getMonthName('Jun'));
        $this->assertEquals('Juli', $format->getMonthName('Jul'));
        $this->assertEquals('Agustus', $format->getMonthName('Aug'));
        $this->assertEquals('September', $format->getMonthName('Sep'));
        $this->assertEquals('Oktober', $format->getMonthName('Oct'));
        $this->assertEquals('November', $format->getMonthName('Nov'));
        $this->assertEquals('Desember', $format->getMonthName('Dec'));
        $this->assertEquals('Foo', $format->getMonthName('Foo'));
    }

    public function testGetLabels(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());
        $format = new IndonesianDateFormat($converter);

        $labels = $format->getLabels();
        $this->assertArrayHasKey('D', $labels);
        $this->assertArrayHasKey('d', $labels);
        $this->assertArrayHasKey('M', $labels);
        $this->assertArrayHasKey('Y', $labels);
        $this->assertEquals('Hari', $labels['D']);
        $this->assertEquals('Tanggal', $labels['d']);
    }
}
