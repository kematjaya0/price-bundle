<?php

namespace Kematjaya\PriceBundle\Tests\Currency;

use Kematjaya\PriceBundle\Converter\IndonesianConverter;
use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Kematjaya\PriceBundle\Tests\AppTestKernel;
use Kematjaya\PriceBundle\Twig\ConverterExtension;
use Kematjaya\PriceBundle\Twig\PriceExtension;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class PriceExtensionTest extends WebTestCase
{
    public static function getKernelClass(): string
    {
        return AppTestKernel::class;
    }

    public function testInstance(): CurrencyFormatInterface
    {
        $container = static::getContainer();
        $this->assertTrue($container->has(CurrencyFormatInterface::class));

        return $container->get(CurrencyFormatInterface::class);
    }

    /**
     * @depends testInstance
     */
    public function testFormatPrice(CurrencyFormatInterface $currencyFormat)
    {
        $bag = $this->createMock(ParameterBagInterface::class);
        $bag->method('get')->with('price')->willReturn([
            'currency' => ['code' => 'IDR', 'cent_limit' => 2, 'cent_point' => ',', 'thousand_point' => '.', 'allow_negative' => true],
        ]);
        $twig = $this->createMock(Environment::class);
        $ext = new PriceExtension($currencyFormat, $bag, $twig);

        $this->assertEquals($currencyFormat->getCurrencySymbol().' 10,000', $ext->price(10000));
    }

    public function testGetTerbilang()
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);
        $ext = new ConverterExtension(new IndonesianConverter($translator));
        $this->assertEquals('sepuluh ribu', strtolower($ext->getTerbilang(10000)));
        $this->assertEquals('sepuluh', strtolower($ext->getTerbilang(10)));
    }
}
