<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Tests\Currency;

use Kematjaya\PriceBundle\Converter\ConverterInterface;
use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Kematjaya\PriceBundle\Tests\AppTestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
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
    public function testIndonesianConvert(ConverterInterface $converter): void
    {
        $this->assertEquals('seratus', trim(strtolower($converter->convert(100))));
        $this->assertEquals('seribu', trim(strtolower($converter->convert(1000))));
        $this->assertEquals('sepuluh ribu', trim(strtolower($converter->convert(10000))));
        $this->assertEquals('satu juta', trim(strtolower($converter->convert(1000000))));

        // No translation catalog is registered in the test kernel, so an
        // explicit currency code is appended to the words as-is.
        $this->assertEquals('seratus idr', trim(strtolower($converter->convert(100, 'IDR'))));
    }

    /**
     * @depends testInstanceCurrencyFormat
     */
    public function testCurrency(CurrencyFormatInterface $currencyFormat): void
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
    public function testParsingCurrency(CurrencyFormatInterface $currencyFormat): void
    {
        $this->assertEquals(10000, $currencyFormat->priceToFloat($currencyFormat->getCurrencySymbol() . '10000'));
        $this->assertEquals($currencyFormat->getCurrencySymbol() . ' 10,000', $currencyFormat->formatPrice(10000));
    }
}