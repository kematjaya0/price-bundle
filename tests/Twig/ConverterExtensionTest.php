<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Tests\Twig;

use Kematjaya\PriceBundle\Converter\ConverterInterface;
use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Kematjaya\PriceBundle\Twig\ConverterExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;

class ConverterExtensionTest extends TestCase
{
    public function testGetFiltersRegistersTerbilang(): void
    {
        $converter = $this->createMock(ConverterInterface::class);
        $extension = new ConverterExtension($converter);

        $filters = $extension->getFilters();

        $this->assertCount(1, $filters);
        $this->assertInstanceOf(TwigFilter::class, $filters[0]);
        $this->assertSame('terbilang', $filters[0]->getName());
    }

    public function testGetTerbilangDelegatesToConverter(): void
    {
        $converter = $this->createMock(ConverterInterface::class);
        $converter->expects($this->once())
            ->method('convert')
            ->with(1000.0, null)
            ->willReturn('seribu');

        $extension = new ConverterExtension($converter);

        $this->assertSame('seribu', $extension->getTerbilang(1000));
    }

    public function testGetTerbilangWithExplicitCurrencyIgnoresIncludeCurrencyFlag(): void
    {
        $converter = $this->createMock(ConverterInterface::class);
        $converter->expects($this->once())
            ->method('convert')
            ->with(1000.0, 'USD')
            ->willReturn('seribu USD');

        $extension = new ConverterExtension($converter);

        $this->assertSame('seribu USD', $extension->getTerbilang(1000, false, 'USD'));
    }

    public function testIncludeCurrencyWithoutCurrencyFormatLeavesCurrencyNull(): void
    {
        $converter = $this->createMock(ConverterInterface::class);
        $converter->expects($this->once())
            ->method('convert')
            ->with(1000.0, null)
            ->willReturn('seribu');

        $extension = new ConverterExtension($converter);

        $this->assertSame('seribu', $extension->getTerbilang(1000, true));
    }

    public function testIncludeCurrencyUsesActiveCurrencyFromCurrencyFormat(): void
    {
        $converter = $this->createMock(ConverterInterface::class);
        $converter->expects($this->once())
            ->method('convert')
            ->with(1000.0, 'IDR')
            ->willReturn('seribu IDR');

        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->method('getCurrency')->willReturn('IDR');

        $extension = new ConverterExtension($converter, $currencyFormat);

        $this->assertSame('seribu IDR', $extension->getTerbilang(1000, true));
    }
}
