<?php

namespace Kematjaya\PriceBundle\Tests\Currency;

use Kematjaya\PriceBundle\Converter\ConverterInterface;
use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Kematjaya\PriceBundle\Tests\AppKernelTest;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

class CurrencyFormatTest //extends WebTestCase 
{
    public static function getKernelClass() 
    {
        return AppKernelTest::class;
    }
    
    /**
     * @param ContainerInterface $container
     * @return ConverterInterface
     */
    public function testInstance(): ConverterInterface
    {
        $container = static::getContainer();
        $this->assertTrue($container->has(ConverterInterface::class));
        
        return $container->get(ConverterInterface::class);
    }
    
    /**
     * @param ContainerInterface $container
     * @return CurrencyFormatInterface
     */
    public function testInstanceCurrencyFormat(): CurrencyFormatInterface
    {
        $container = static::getContainer();
        $this->assertTrue($container->has(CurrencyFormatInterface::class));
        
        return $container->get(CurrencyFormatInterface::class);
    }
    
    /**
     * @depends testInstance
     * @param ConverterInterface $converter
     */
    public function testIndonesianConvert(ConverterInterface $converter)
    {
        $this->assertEquals('seratus', trim(strtolower($converter->convert(100))));
        $this->assertEquals('seratus rupiah', trim(strtolower($converter->convert(100, true))));
        
        $this->assertEquals('seribu', trim(strtolower($converter->convert(1000))));
        $this->assertEquals('seribu rupiah', trim(strtolower($converter->convert(1000, true))));
        
        $this->assertEquals('sepuluh ribu', trim(strtolower($converter->convert(10000))));
        $this->assertEquals('sepuluh ribu rupiah', trim(strtolower($converter->convert(10000, true))));
        
        $this->assertEquals('satu juta', trim(strtolower($converter->convert(1000000))));
        $this->assertEquals('satu juta rupiah', trim(strtolower($converter->convert(1000000, true))));
    }
    
    /**
     * @depends testInstanceCurrencyFormat
     * @param CurrencyFormatInterface $currencyFormat
     */
    public function testCurrency(CurrencyFormatInterface $currencyFormat)
    {
        $this->assertEquals("IDR", $currencyFormat->getCurrencySymbol());
        
        $currencyFormat->setCurrency("USD");
        $this->assertEquals("$", $currencyFormat->getCurrencySymbol());
        
        $this->expectExceptionMessage(sprintf("%s not supported", "IDD"));
        $currencyFormat->setCurrency("IDD");
    }
    
    /**
     * @depends testInstanceCurrencyFormat
     * @param CurrencyFormatInterface $currencyFormat
     */
    public function testParsingCurrency(CurrencyFormatInterface $currencyFormat)
    {
        $this->assertEquals(10000, $currencyFormat->priceToFloat($currencyFormat->getCurrencySymbol().'10000'));
        $this->assertEquals($currencyFormat->getCurrencySymbol().' 10,000', $currencyFormat->formatPrice(10000));
    }
    
}
