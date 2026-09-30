<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Tests\Lib;

use Kematjaya\PriceBundle\Lib\CurrencyFormat;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * Pure unit tests for CurrencyFormat, built directly against a mocked
 * ContainerBagInterface so every branch can be exercised without booting a
 * Symfony kernel. See tests/Currency/CurrencyFormatTest.php for the
 * container/DI integration test.
 */
class CurrencyFormatTest extends TestCase
{
    private function buildCurrencyFormat(array $overrides = []): CurrencyFormat
    {
        $currencyConfig = array_merge([
            'code' => 'IDR',
            'cent_limit' => 0,
            'cent_point' => '.',
            'thousand_point' => ',',
            'cent_limits' => [],
        ], $overrides);

        $container = $this->createMock(ContainerBagInterface::class);
        $container->method('get')->with('price')->willReturn(['currency' => $currencyConfig]);

        return new CurrencyFormat($container);
    }

    public function testConstructorRejectsUnsupportedDefaultCurrency(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('XXX not supported');

        $this->buildCurrencyFormat(['code' => 'XXX']);
    }

    public function testGetters(): void
    {
        $currencyFormat = $this->buildCurrencyFormat(
            ['code' => 'USD', 'cent_limit' => 2, 'cent_point' => ',', 'thousand_point' => '.']
        );

        $this->assertSame('USD', $currencyFormat->getCurrency());
        $this->assertSame('$', $currencyFormat->getCurrencySymbol());
        $this->assertSame(2, $currencyFormat->getCentLimit());
        $this->assertSame(',', $currencyFormat->getCentPoint());
        $this->assertSame('.', $currencyFormat->getThousandPoint());
    }

    public function testSetCentLimitOverridesTheGlobalDefault(): void
    {
        $currencyFormat = $this->buildCurrencyFormat(['cent_limit' => 0]);

        $currencyFormat->setCentLimit(3);

        $this->assertSame(3, $currencyFormat->getCentLimit());
    }

    public function testSetCurrencyChangesTheActiveCurrency(): void
    {
        $currencyFormat = $this->buildCurrencyFormat(['code' => 'IDR']);

        $result = $currencyFormat->setCurrency('USD');

        $this->assertSame($currencyFormat, $result);
        $this->assertSame('USD', $currencyFormat->getCurrency());
    }

    public function testSetCurrencyRejectsUnsupportedCode(): void
    {
        $currencyFormat = $this->buildCurrencyFormat();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('IDD not supported');

        $currencyFormat->setCurrency('IDD');
    }

    public function testGetCentLimitByCurrencyUsesOverrideWhenConfigured(): void
    {
        $currencyFormat = $this->buildCurrencyFormat(['cent_limit' => 0, 'cent_limits' => ['USD' => 2]]);

        $this->assertSame(2, $currencyFormat->getCentLimitByCurrency('USD'));
        $this->assertSame(0, $currencyFormat->getCentLimitByCurrency('IDR'));
        $this->assertSame(['USD' => 2], $currencyFormat->getCentLimits());
    }

    public function testChangingConfiguredLimitChangesRounding(): void
    {
        $twoDecimals = $this->buildCurrencyFormat(['cent_limits' => ['USD' => 2]]);
        $this->assertSame(23.33, $twoDecimals->priceToFloat('23.325', 'USD'));

        $threeDecimals = $this->buildCurrencyFormat(['cent_limits' => ['USD' => 3]]);
        $this->assertSame(23.325, $threeDecimals->priceToFloat('23.325', 'USD'));
    }

    public function testPriceToFloatUsesDefaultCurrencyWhenNoneGiven(): void
    {
        // Currencies::getSymbol('IDR') has no dedicated glyph in CLDR data
        // and returns the code itself, "IDR".
        $currencyFormat = $this->buildCurrencyFormat(['code' => 'IDR']);

        $this->assertSame(10000.0, $currencyFormat->priceToFloat('IDR10000'));
    }

    public function testPriceToFloatFallsBackToRawCurrencyStringWhenNotAValidIsoCode(): void
    {
        $currencyFormat = $this->buildCurrencyFormat(['cent_point' => '.', 'thousand_point' => ',']);

        // "Pts" is not a real ISO currency code, so isValid() throws inside
        // the try/catch and the literal string is used as the "symbol" to
        // strip from the input instead.
        $this->assertSame(1000.0, $currencyFormat->priceToFloat('Pts 1,000', 'Pts'));
    }

    public function testPriceToFloatHonoursExplicitOverrides(): void
    {
        $currencyFormat = $this->buildCurrencyFormat(['cent_point' => '.', 'thousand_point' => ',']);

        $this->assertSame(1234.5, $currencyFormat->priceToFloat('1.234,50', 'IDR', 1, ',', '.'));
    }

    public function testFormatPriceUsesDefaultCurrencyWhenNoneGiven(): void
    {
        $currencyFormat = $this->buildCurrencyFormat(['code' => 'IDR']);

        $this->assertSame('IDR 10,000', $currencyFormat->formatPrice(10000));
    }

    public function testFormatPriceHonoursExplicitOverrides(): void
    {
        $currencyFormat = $this->buildCurrencyFormat();

        $this->assertSame('$ 1.000,50', $currencyFormat->formatPrice(1000.5, 2, ',', '.', 'USD'));
    }

    public function testFormatPriceRejectsUnsupportedCurrency(): void
    {
        $currencyFormat = $this->buildCurrencyFormat();

        $this->expectException(\InvalidArgumentException::class);

        $currencyFormat->formatPrice(1000, null, null, null, 'XXX');
    }
}
