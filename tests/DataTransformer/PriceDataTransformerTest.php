<?php

namespace Kematjaya\PriceBundle\Tests\DataTransformer;

use Kematjaya\PriceBundle\DataTransformer\PriceDataTransformer;
use Kematjaya\PriceBundle\Lib\CurrencyFormat;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class PriceDataTransformerTest extends TestCase
{
    private function createCurrencyFormat(): CurrencyFormat
    {
        $container = $this->createMock(ContainerBagInterface::class);
        $container->method('get')->with('price')->willReturn([
            'currency' => [
                'code' => 'IDR', 'cent_limit' => 2, 'cent_point' => ',', 'thousand_point' => '.', 'allow_negative' => true,
            ],
        ]);

        return new CurrencyFormat($container);
    }

    public function testReverseTransform(): void
    {
        $cf = $this->createCurrencyFormat();
        $transformer = new PriceDataTransformer($cf);

        $result = $transformer->reverseTransform('IDR 10.000,50');
        $this->assertEquals(10000.50, $result);
    }

    public function testReverseTransformEmpty(): void
    {
        $cf = $this->createCurrencyFormat();
        $transformer = new PriceDataTransformer($cf);

        $this->assertEquals(0, $transformer->reverseTransform(''));
        $this->assertEquals(0, $transformer->reverseTransform(null));
    }

    public function testTransform(): void
    {
        $cf = $this->createCurrencyFormat();
        $transformer = new PriceDataTransformer($cf);

        $result = $transformer->transform(10000.50);
        $this->assertEquals(100005.0, $result);
    }

    public function testTransformEmpty(): void
    {
        $cf = $this->createCurrencyFormat();
        $transformer = new PriceDataTransformer($cf);

        $this->assertEquals(0, $transformer->transform(''));
        $this->assertEquals(0, $transformer->transform(null));
    }
}
