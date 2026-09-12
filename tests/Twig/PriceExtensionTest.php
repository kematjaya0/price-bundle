<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Tests\Twig;

use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Kematjaya\PriceBundle\Twig\PriceExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;
use Twig\TwigFunction;

class PriceExtensionTest extends TestCase
{
    private function createBag(bool $allowNegative = true): ParameterBagInterface
    {
        $bag = $this->createMock(ParameterBagInterface::class);
        $bag->method('get')
            ->with('price')
            ->willReturn(['currency' => ['allow_negative' => $allowNegative]]);

        return $bag;
    }

    public function testGetFunctionsReturnsExpectedNames(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $twig = $this->createMock(Environment::class);
        $extension = new PriceExtension($currencyFormat, $this->createBag(), $twig);

        $names = array_map(function (TwigFunction $function): string {
            return $function->getName();
        }, $extension->getFunctions());

        $this->assertSame(
            [
                'render_price_javascript', 'currency', 'price', 'price_symbol',
                'thousand_point', 'cent_point', 'cent_limit', 'allow_negative',
            ],
            $names
        );
    }

    public function testRenderPriceJavascriptDelegatesToTwig(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())
            ->method('render')
            ->with('@Price/javascripts.twig')
            ->willReturn('<script></script>');

        $extension = new PriceExtension($currencyFormat, $this->createBag(), $twig);
        $callback = $this->findFunctionCallable($extension, 'render_price_javascript');

        $this->assertSame('<script></script>', $callback());
    }

    public function testPriceSymbolThousandPointCentPointAndCentLimitDelegateToCurrencyFormat(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->method('getCurrencySymbol')->willReturn('Rp');
        $currencyFormat->method('getThousandPoint')->willReturn(',');
        $currencyFormat->method('getCentPoint')->willReturn('.');
        $currencyFormat->method('getCentLimit')->willReturn(0);

        $extension = new PriceExtension($currencyFormat, $this->createBag(), $this->createMock(Environment::class));

        $this->assertSame('Rp', $this->findFunctionCallable($extension, 'price_symbol')());
        $this->assertSame(',', $this->findFunctionCallable($extension, 'thousand_point')());
        $this->assertSame('.', $this->findFunctionCallable($extension, 'cent_point')());
        $this->assertSame(0, $this->findFunctionCallable($extension, 'cent_limit')());
    }

    public function testAllowNegativeReadsFromConfig(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $twig = $this->createMock(Environment::class);
        $extension = new PriceExtension($currencyFormat, $this->createBag(false), $twig);

        $this->assertFalse($this->findFunctionCallable($extension, 'allow_negative')());
    }

    public function testPriceFormatsNumericValue(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->expects($this->once())
            ->method('formatPrice')
            ->with(1000.0, null, null, null, null)
            ->willReturn('Rp 1,000');

        $extension = new PriceExtension($currencyFormat, $this->createBag(), $this->createMock(Environment::class));

        $this->assertSame('Rp 1,000', $extension->price(1000));
    }

    public function testPriceFallsBackToZeroForNonNumericInput(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->expects($this->once())
            ->method('formatPrice')
            ->with(0.0, null, null, null, null)
            ->willReturn('Rp 0');

        $extension = new PriceExtension($currencyFormat, $this->createBag(), $this->createMock(Environment::class));

        $this->assertSame('Rp 0', $extension->price('not-a-number'));
    }

    public function testCurrencyDelegatesToPriceWithCurrencyArgument(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->expects($this->once())
            ->method('formatPrice')
            ->with(1000.0, 2, '.', ',', 'USD')
            ->willReturn('$ 1,000.00');

        $extension = new PriceExtension($currencyFormat, $this->createBag(), $this->createMock(Environment::class));

        $this->assertSame('$ 1,000.00', $extension->currency(1000, 'USD', 2, '.', ','));
    }

    /**
     * @return callable
     */
    private function findFunctionCallable(PriceExtension $extension, string $name)
    {
        foreach ($extension->getFunctions() as $function) {
            if ($name === $function->getName()) {
                return $function->getCallable();
            }
        }

        throw new \RuntimeException(sprintf('twig function "%s" not found', $name));
    }
}
