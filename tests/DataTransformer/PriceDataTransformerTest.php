<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Tests\DataTransformer;

use Kematjaya\PriceBundle\DataTransformer\PriceDataTransformer;
use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use PHPUnit\Framework\TestCase;

class PriceDataTransformerTest extends TestCase
{
    public function testTransformFormatsANonEmptyModelValue(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->expects($this->once())
            ->method('formatPrice')
            ->with(1000.0)
            ->willReturn('IDR 1,000');

        $transformer = new PriceDataTransformer($currencyFormat);

        $this->assertSame('IDR 1,000', $transformer->transform(1000.0));
    }

    public function testTransformReturnsNullForEmptyModelValue(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->expects($this->never())->method('formatPrice');

        $transformer = new PriceDataTransformer($currencyFormat);

        $this->assertNull($transformer->transform(null));
        $this->assertNull($transformer->transform(0));
    }

    public function testReverseTransformParsesANonEmptyViewValue(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->expects($this->once())
            ->method('priceToFloat')
            ->with('IDR 1,000')
            ->willReturn(1000.0);

        $transformer = new PriceDataTransformer($currencyFormat);

        $this->assertSame(1000.0, $transformer->reverseTransform('IDR 1,000'));
    }

    public function testReverseTransformReturnsZeroForEmptyViewValue(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->expects($this->never())->method('priceToFloat');

        $transformer = new PriceDataTransformer($currencyFormat);

        $this->assertSame(0.0, $transformer->reverseTransform(null));
        $this->assertSame(0.0, $transformer->reverseTransform(''));
    }
}