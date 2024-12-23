<?php

namespace Kematjaya\PriceBundle\Tests\Currency;

use Kematjaya\PriceBundle\Tests\AppKernelTest;
use Kematjaya\PriceBundle\Converter\IndonesianConverter;
use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Kematjaya\PriceBundle\Twig\PriceExtension;
use Kematjaya\PriceBundle\Twig\ConverterExtension;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class PriceExtensionTest extends WebTestCase
{
    public static function getKernelClass() :string
    {
        return AppKernelTest::class;
    }
    
    /**
     * @param ContainerInterface $container
     * @return CurrencyFormatInterface
     */
    public function testInstance(): CurrencyFormatInterface
    {
        $container = static::getContainer();
        $this->assertTrue($container->has(CurrencyFormatInterface::class));
        
        return $container->get(CurrencyFormatInterface::class);
    }
    
    /**
     * @depends testInstance
     * @param CurrencyFormatInterface $currencyFormat
     */
    public function testFormatPrice(CurrencyFormatInterface $currencyFormat)
    {
        $ext = new PriceExtension($currencyFormat);
        
        $this->assertEquals($currencyFormat->getCurrencySymbol().' 10,000', $ext->price(10000));
    }
    
    public function testGetTerbilang()
    {
        $ext = new ConverterExtension(new IndonesianConverter());
        $this->assertEquals("sepuluh ribu", strtolower($ext->getTerbilang(10000)));
        $this->assertEquals("sepuluh", strtolower($ext->getTerbilang(10)));
    }
}
