<?php

/**
 * This file is part of the kematjaya-currency-lib.
 */

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Tests\Lib;

use DateTime;
use Kematjaya\PriceBundle\Converter\IndonesianConverter;
use Kematjaya\PriceBundle\Lib\IndonesianDateFormat;
use PHPUnit\Framework\TestCase;

/**
 * @package Kematjaya\Currency\Tests
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class IndonesianDateFormatTest extends TestCase
{
    private function createFormat(): IndonesianDateFormat
    {
        return new IndonesianDateFormat(new IndonesianConverter());
    }

    public function testFormatTranslatesDayAndMonthTokens(): void
    {
        $date = new DateTime('2021-01-17 00:00:00');

        $this->assertEquals('17 Januari 2021 00:00:00', $this->createFormat()->format($date, 'd M Y H:i:s'));
    }

    public function testFormatWithoutASupportedSeparatorReturnsRawDateFormat(): void
    {
        $date = new DateTime('2021-01-17 00:00:00');

        $this->assertEquals($date->format('Y'), $this->createFormat()->format($date, 'Y'));
    }

    public function testConvertToStringSpellsOutNumericTokensWithLabels(): void
    {
        $date = new DateTime('2021-01-17 00:00:00');

        $expected = 'Hari Minggu Tanggal Tujuh Belas Bulan Januari Tahun Dua Ribu Dua Puluh Satu';
        $this->assertEquals($expected, $this->createFormat()->convertToString($date, 'D d M Y'));
    }

    public function testConvertToStringWithoutASupportedSeparatorReturnsRawDateFormat(): void
    {
        $date = new DateTime('2021-01-17 00:00:00');

        $this->assertEquals($date->format('Y'), $this->createFormat()->convertToString($date, 'Y'));
    }

    public function testGetDayNameFallsBackToInputForUnknownDay(): void
    {
        $this->assertSame('Senin', $this->createFormat()->getDayName('Mon'));
        $this->assertSame('Unknown', $this->createFormat()->getDayName('Unknown'));
    }

    public function testGetMonthNameFallsBackToInputForUnknownMonth(): void
    {
        $this->assertSame('Januari', $this->createFormat()->getMonthName('Jan'));
        $this->assertSame('Unknown', $this->createFormat()->getMonthName('Unknown'));
    }

    public function testGetLabels(): void
    {
        $this->assertSame(
            ['D' => 'Hari', 'd' => 'Tanggal', 'M' => 'Bulan', 'Y' => 'Tahun'],
            $this->createFormat()->getLabels()
        );
    }

    public function testReverseReturnsNullForEmptyString(): void
    {
        $this->assertNull($this->createFormat()->reverse(''));
    }

    public function testReverseReturnsNullWhenDatePartIsMissingBeforeTheSeparator(): void
    {
        // A leading separator makes the date segment filter out as empty,
        // shifting the time segment to index 1 with no index 0 left.
        $this->assertNull($this->createFormat()->reverse(',12:09'));
    }

    public function testReverseReturnsNullForMalformedDatePart(): void
    {
        $this->assertNull($this->createFormat()->reverse('05 Juli'));
    }

    public function testReverseReturnsNullForUnknownMonthName(): void
    {
        $this->assertNull($this->createFormat()->reverse('05 Unknown 2018'));
    }

    public function testReverseWithoutTimeReturnsMidnight(): void
    {
        $date = $this->createFormat()->reverse('05 Juli 2018');

        $this->assertNotNull($date);
        $this->assertSame('2018-07-05 00:00:00', $date->format('Y-m-d H:i:s'));
    }

    public function testReverseWithHourAndMinute(): void
    {
        $date = $this->createFormat()->reverse('05 Juli 2018,12:09');

        $this->assertNotNull($date);
        $this->assertSame('2018-07-05 12:09:00', $date->format('Y-m-d H:i:s'));
    }

    public function testReverseWithHourOnlyDefaultsMinuteToZero(): void
    {
        $date = $this->createFormat()->reverse('05 Juli 2018,12');

        $this->assertNotNull($date);
        $this->assertSame('2018-07-05 12:00:00', $date->format('Y-m-d H:i:s'));
    }

    public function testReverseWithWhitespaceOnlyTimeSegmentDefaultsToMidnight(): void
    {
        $date = $this->createFormat()->reverse('05 Juli 2018, ');

        $this->assertNotNull($date);
        $this->assertSame('2018-07-05 00:00:00', $date->format('Y-m-d H:i:s'));
    }

    public function testReverseWithCustomSplitTimeSeparator(): void
    {
        $date = $this->createFormat()->reverse('05 Juli 2018|12:09', '|');

        $this->assertNotNull($date);
        $this->assertSame('2018-07-05 12:09:00', $date->format('Y-m-d H:i:s'));
    }
}
