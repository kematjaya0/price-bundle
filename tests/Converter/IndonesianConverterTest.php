<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Tests\Converter;

use Kematjaya\PriceBundle\Converter\IndonesianConverter;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndonesianConverterTest extends TestCase
{
    /**
     * @dataProvider provideNumbers
     */
    public function testConvertWithoutCurrency(float $number, string $expected): void
    {
        $converter = new IndonesianConverter();

        $this->assertSame($expected, $converter->convert($number));
    }

    public function provideNumbers(): array
    {
        return [
            'zero' => [0, 'Nol'],
            'single digit' => [5, 'Lima'],
            'ten' => [10, 'Sepuluh'],
            'eleven' => [11, 'Sebelas'],
            'twelve (belas)' => [12, 'Dua Belas'],
            'nineteen (belas)' => [19, 'Sembilan Belas'],
            'twenty (puluh)' => [20, 'Dua Puluh'],
            'forty five (puluh)' => [45, 'Empat Puluh Lima'],
            'ninety nine (puluh)' => [99, 'Sembilan Puluh Sembilan'],
            'one hundred (seratus)' => [100, 'Seratus'],
            'one hundred fifty (seratus)' => [150, 'Seratus Lima Puluh'],
            'two hundred (ratus)' => [200, 'Dua Ratus'],
            'nine hundred ninety nine (ratus)' => [999, 'Sembilan Ratus Sembilan Puluh Sembilan'],
            'one thousand (seribu)' => [1000, 'Seribu'],
            'one thousand five hundred (seribu)' => [1500, 'Seribu Lima Ratus'],
            'two thousand (ribu)' => [2000, 'Dua Ribu'],
            'ten thousand (ribu)' => [10000, 'Sepuluh Ribu'],
            'one million (juta)' => [1000000, 'Satu Juta'],
            'one billion (milyar)' => [1000000000, 'Satu Milyar'],
            'one trillion (trilyun)' => [1000000000000, 'Satu Trilyun'],
            'largest supported number' => [
                99999999999999,
                'Sembilan Puluh Sembilan Trilyun Sembilan Ratus Sembilan Puluh Sembilan Milyar '
                . 'Sembilan Ratus Sembilan Puluh Sembilan Juta Sembilan Ratus Sembilan Puluh Sembilan Ribu '
                . 'Sembilan Ratus Sembilan Puluh Sembilan',
            ],
            'negative' => [-100, 'Minus Seratus'],
            'decimal (koma)' => [100.25, 'Seratus Koma Dua Puluh Lima'],
        ];
    }

    public function testConvertThrowsWhenNumberExceedsSupportedRange(): void
    {
        $converter = new IndonesianConverter();

        $this->expectException(\OverflowException::class);
        $this->expectExceptionMessage('angka melebihi batas');

        $converter->convert(100000000000000);
    }

    public function testConvertWithCurrencyButNoTranslatorUsesRawCurrencyCode(): void
    {
        $converter = new IndonesianConverter();

        $this->assertSame('Seratus IDR', $converter->convert(100, 'IDR'));
    }

    public function testConvertWithCurrencyAndTranslatorUsesTranslatedLabel(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->once())
            ->method('trans')
            ->with('IDR')
            ->willReturn('Rupiah');

        $converter = new IndonesianConverter($translator);

        $this->assertSame('Seratus Rupiah', $converter->convert(100, 'IDR'));
    }

    public function testConvertNegativeWithCurrencyAppendsCurrencyOnlyOnce(): void
    {
        $converter = new IndonesianConverter();

        $this->assertSame('Minus Seratus IDR', $converter->convert(-100, 'IDR'));
    }
}
