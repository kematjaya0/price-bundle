<?php

namespace Kematjaya\PriceBundle\Tests\Lib;

use Kematjaya\PriceBundle\Lib\CurrencyFormat;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class CurrencyFormatUnitTest extends TestCase
{
    private function createCurrencyFormat(array $configOverrides = []): CurrencyFormat
    {
        $defaults = [
            'code' => 'IDR',
            'cent_limit' => 0,
            'cent_point' => '.',
            'thousand_point' => ',',
            'allow_negative' => true,
        ];

        $configs = array_merge($defaults, $configOverrides);

        $container = $this->createMock(ContainerBagInterface::class);
        $container->method('get')->with('price')->willReturn(['currency' => $configs]);

        return new CurrencyFormat($container);
    }

    public function testDefaultConfig(): void
    {
        $cf = $this->createCurrencyFormat();

        $this->assertEquals('IDR', $cf->getCurrency());
        $this->assertEquals(0, $cf->getCentLimit());
        $this->assertEquals('.', $cf->getCentPoint());
        $this->assertEquals(',', $cf->getThousandPoint());
    }

    public function testGetCurrencySymbol(): void
    {
        $cf = $this->createCurrencyFormat();
        $this->assertEquals('IDR', $cf->getCurrencySymbol());
    }

    public function testSetCurrency(): void
    {
        $cf = $this->createCurrencyFormat();
        $cf->setCurrency('USD');
        $this->assertEquals('USD', $cf->getCurrency());
        $this->assertEquals('$', $cf->getCurrencySymbol());
    }

    public function testSetCurrencyInvalid(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not supported');

        $cf = $this->createCurrencyFormat();
        $cf->setCurrency('XXX');
    }

    public function testSetCentLimit(): void
    {
        $cf = $this->createCurrencyFormat();
        $cf->setCentLimit(2);
        $this->assertEquals(2, $cf->getCentLimit());
    }

    public function testFormatPriceDefault(): void
    {
        $cf = $this->createCurrencyFormat();
        $this->assertEquals('IDR 10,000', $cf->formatPrice(10000));
        $this->assertEquals('IDR 0', $cf->formatPrice(0));
    }

    public function testFormatPriceWithDecimals(): void
    {
        $cf = $this->createCurrencyFormat([
            'cent_limit' => 2, 'cent_point' => ',', 'thousand_point' => '.',
        ]);

        $this->assertEquals('IDR 10.000,00', $cf->formatPrice(10000));
        $this->assertEquals('IDR 1.234,56', $cf->formatPrice(1234.56));
    }

    public function testFormatPriceCustomCurrency(): void
    {
        $cf = $this->createCurrencyFormat();
        $result = $cf->formatPrice(500, 2, '.', ',', 'USD');
        $this->assertEquals('$ 500.00', $result);
    }

    public function testPriceToFloatSimple(): void
    {
        $cf = $this->createCurrencyFormat();
        $this->assertEquals(10000.0, $cf->priceToFloat('IDR10000'));
        $this->assertEquals(0.0, $cf->priceToFloat('0'));
    }

    public function testPriceToFloatWithThousandsSeparator(): void
    {
        $cf = $this->createCurrencyFormat([
            'cent_limit' => 2, 'cent_point' => ',', 'thousand_point' => '.',
        ]);

        $this->assertEquals(10000.0, $cf->priceToFloat('IDR 10.000'));
        $this->assertEquals(12345.67, $cf->priceToFloat('IDR 12.345,67'));
    }

    public function testPriceToFloatWithDifferentSymbol(): void
    {
        $cf = $this->createCurrencyFormat([
            'code' => 'USD', 'cent_limit' => 2, 'cent_point' => '.', 'thousand_point' => ',',
        ]);

        $this->assertEquals(1234.0, $cf->priceToFloat('$1,234'));
    }

    public function testPriceToFloatWithCustomParams(): void
    {
        $cf = $this->createCurrencyFormat();
        $this->assertEquals(1234.56, $cf->priceToFloat('€ 1.234,56', 'EUR', 2, ',', '.'));
    }

    public function testPriceToFloatInvalidSymbol(): void
    {
        $cf = $this->createCurrencyFormat();
        $result = $cf->priceToFloat('XYZ 1.234', 'XYZ', 2, '.', ',');
        $this->assertEquals(1.23, $result);
    }
}
