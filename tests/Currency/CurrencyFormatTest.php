<?php

namespace Kematjaya\PriceBundle\Tests\Currency;

use Kematjaya\PriceBundle\Converter\ConverterInterface;
use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Kematjaya\PriceBundle\Tests\AppTestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CurrencyFormatTest extends WebTestCase
{
    public static function getKernelClass(): string
    {
        return AppTestKernel::class;
    }

    public function testInstance(): ConverterInterface
    {
        $container = static::getContainer();
        $this->assertTrue($container->has(ConverterInterface::class));

        return $container->get(ConverterInterface::class);
    }

    public function testInstanceCurrencyFormat(): CurrencyFormatInterface
    {
        $container = static::getContainer();
        $this->assertTrue($container->has(CurrencyFormatInterface::class));

        return $container->get(CurrencyFormatInterface::class);
    }

    /**
     * @depends testInstance
     */
    public function testIndonesianConvert(ConverterInterface $converter)
    {
        $this->assertEquals('seratus', trim(strtolower($converter->convert(100))));
        $this->assertEquals('seratus rupiah', trim(strtolower($converter->convert(100, 'Rupiah'))));

        $this->assertEquals('seribu', trim(strtolower($converter->convert(1000))));
        $this->assertEquals('seribu rupiah', trim(strtolower($converter->convert(1000, 'Rupiah'))));

        $this->assertEquals('sepuluh ribu', trim(strtolower($converter->convert(10000))));
        $this->assertEquals('sepuluh ribu rupiah', trim(strtolower($converter->convert(10000, 'Rupiah'))));

        $this->assertEquals('satu juta', trim(strtolower($converter->convert(1000000))));
        $this->assertEquals('satu juta rupiah', trim(strtolower($converter->convert(1000000, 'Rupiah'))));
    }

    /**
     * @depends testInstanceCurrencyFormat
     */
    public function testCurrency(CurrencyFormatInterface $currencyFormat)
    {
        $this->assertEquals('IDR', $currencyFormat->getCurrencySymbol());

        $currencyFormat->setCurrency('USD');
        $this->assertEquals('$', $currencyFormat->getCurrencySymbol());

        $this->expectExceptionMessage(sprintf('%s not supported', 'IDD'));
        $currencyFormat->setCurrency('IDD');
    }

    /**
     * @depends testInstanceCurrencyFormat
     */
    public function testParsingCurrency(CurrencyFormatInterface $currencyFormat)
    {
        $this->assertEquals(10000, $currencyFormat->priceToFloat($currencyFormat->getCurrencySymbol().'10000'));
        $this->assertEquals($currencyFormat->getCurrencySymbol().' 10,000', $currencyFormat->formatPrice(10000));
    }
}
