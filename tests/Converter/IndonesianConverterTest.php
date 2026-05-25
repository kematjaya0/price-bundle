<?php

namespace Kematjaya\PriceBundle\Tests\Converter;

use Kematjaya\PriceBundle\Converter\IndonesianConverter;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndonesianConverterTest extends TestCase
{
    private function createTranslator(): TranslatorInterface
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return $translator;
    }

    public function testConvertBasicNumbers(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());

        $this->assertEquals('Nol', $converter->convert(0));
        $this->assertEquals('Satu', $converter->convert(1));
        $this->assertEquals('Dua', $converter->convert(2));
        $this->assertEquals('Sepuluh', $converter->convert(10));
        $this->assertEquals('Sebelas', $converter->convert(11));
        $this->assertEquals('Dua Belas', $converter->convert(12));
        $this->assertEquals('Dua Puluh', $converter->convert(20));
        $this->assertEquals('Dua Puluh Satu', $converter->convert(21));
    }

    public function testConvertHundreds(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());

        $this->assertEquals('Seratus', $converter->convert(100));
        $this->assertEquals('Seratus Satu', $converter->convert(101));
        $this->assertEquals('Seratus Sepuluh', $converter->convert(110));
        $this->assertEquals('Dua Ratus', $converter->convert(200));
        $this->assertEquals('Sembilan Ratus Sembilan Puluh Sembilan', $converter->convert(999));
    }

    public function testConvertThousands(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());

        $this->assertEquals('Seribu', $converter->convert(1000));
        $this->assertEquals('Seribu Seratus', $converter->convert(1100));
        $this->assertEquals('Dua Ribu', $converter->convert(2000));
        $this->assertEquals('Sepuluh Ribu', $converter->convert(10000));
        $this->assertEquals('Seratus Ribu', $converter->convert(100000));
    }

    public function testConvertMillions(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());

        $this->assertEquals('Satu Juta', $converter->convert(1000000));
        $this->assertEquals('Dua Juta', $converter->convert(2000000));
        $this->assertEquals('Sepuluh Juta', $converter->convert(10000000));
        $this->assertEquals('Seratus Juta', $converter->convert(100000000));
    }

    public function testConvertBillions(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());

        $this->assertEquals('Satu Milyar', $converter->convert(1000000000));
        $this->assertEquals('Dua Milyar', $converter->convert(2000000000));
        $this->assertEquals('Seratus Milyar', $converter->convert(100000000000));
    }

    public function testConvertTrillions(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());

        $this->assertEquals('Satu Trilyun', $converter->convert(1000000000000));
        $this->assertEquals('Dua Trilyun', $converter->convert(2000000000000));
    }

    public function testConvertWithDecimal(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());

        $this->assertEquals('Seratus Koma Lima', $converter->convert(100.5));
        $this->assertEquals('Seratus Koma Dua Puluh Lima', $converter->convert(100.25));
    }

    public function testConvertWithCurrency(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());

        $this->assertEquals('Seratus Rupiah', $converter->convert(100, 'Rupiah'));
        $this->assertEquals('Seribu Dolar', $converter->convert(1000, 'Dolar'));
    }

    public function testConvertNegativeNumber(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());

        $result = $converter->convert(-100);
        $this->assertStringContainsString('Minus', $result);
        $this->assertStringContainsString('Seratus', $result);
    }

    public function testConvertNegativeWithCurrency(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());

        $result = $converter->convert(-5000, 'Rupiah');
        $this->assertStringContainsString('Minus', $result);
        $this->assertStringContainsString('Rupiah', $result);
    }

    public function testConvertZero(): void
    {
        $converter = new IndonesianConverter($this->createTranslator());

        $this->assertEquals('Nol', $converter->convert(0));
    }
}
