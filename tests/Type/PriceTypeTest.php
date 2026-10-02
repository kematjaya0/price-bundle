<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Tests\Type;

use Kematjaya\PriceBundle\Lib\CurrencyFormat;
use Kematjaya\PriceBundle\Type\PriceType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\PreloadedExtension;

class PriceTypeTest extends TestCase
{
    private function createFactory(array $currencyConfig, bool $allowNegative = true): FormFactoryInterface
    {
        $currencyConfig += [
            'code' => 'IDR',
            'cent_limit' => 0,
            'cent_point' => '.',
            'thousand_point' => ',',
            'cent_limits' => [],
        ];

        $container = $this->createMock(ContainerBagInterface::class);
        $container->method('get')->with('price')->willReturn(['currency' => $currencyConfig]);
        $currencyFormat = new CurrencyFormat($container);

        $bag = $this->createMock(ParameterBagInterface::class);
        $bag->method('get')->with('price')->willReturn([
            'currency' => $currencyConfig + ['allow_negative' => $allowNegative],
        ]);

        $priceType = new PriceType($currencyFormat, $bag);

        return Forms::createFormFactoryBuilder()
            ->addExtension(new PreloadedExtension([$priceType], []))
            ->getFormFactory();
    }

    #[DataProvider('provideFormattedValues')]
    public function testSubmitParsesFormattedValueUsingConfiguredScale(string $submitted, float $expected): void
    {
        $factory = $this->createFactory(
            ['code' => 'IDR', 'cent_limit' => 2, 'cent_point' => ',', 'thousand_point' => '.']
        );

        $form = $factory->create(PriceType::class);
        $form->submit($submitted);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($expected, $form->getData());
    }

    public static function provideFormattedValues(): array
    {
        return [
            ['IDR 20.300,02', 20300.02],
            ['IDR 20.000,02', 20000.02],
            ['IDR 20.300,52', 20300.52],
            ['IDR128.888,45', 128888.45],
        ];
    }

    public function testSubmitWithZeroScaleRoundsToWholeNumber(): void
    {
        $factory = $this->createFactory(
            ['code' => 'IDR', 'cent_limit' => 0, 'cent_point' => ',', 'thousand_point' => '.']
        );

        $form = $factory->create(PriceType::class);
        $form->submit('IDR 1.235');

        $this->assertTrue($form->isSynchronized());
        $this->assertSame(1235.0, $form->getData());
    }

    public function testSubmitNullYieldsZero(): void
    {
        $factory = $this->createFactory(['code' => 'IDR']);

        $form = $factory->create(PriceType::class);
        $form->submit(null);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame(0.0, $form->getData());
    }

    public function testScaleDefaultsFromCurrencyWhenNotExplicitlyGiven(): void
    {
        $factory = $this->createFactory(['code' => 'IDR', 'cent_limit' => 0, 'cent_limits' => ['USD' => 2]]);

        $usdForm = $factory->create(PriceType::class, null, ['currency' => 'USD']);
        $idrForm = $factory->create(PriceType::class, null, ['currency' => 'IDR']);

        $this->assertSame(2, $usdForm->getConfig()->getOption('scale'));
        $this->assertSame(0, $idrForm->getConfig()->getOption('scale'));
    }

    public function testExplicitScaleOverridesTheDerivedOne(): void
    {
        $factory = $this->createFactory(['code' => 'IDR']);

        $form = $factory->create(PriceType::class, null, ['scale' => 5]);

        $this->assertSame(5, $form->getConfig()->getOption('scale'));
    }

    public function testModelValueIsPaddedToScaleWhenRenderedInTheView(): void
    {
        $factory = $this->createFactory(
            ['code' => 'USD', 'cent_limit' => 2, 'cent_point' => '.', 'thousand_point' => ',']
        );

        $form = $factory->create(PriceType::class, 5.0, ['currency' => 'USD']);
        $view = $form->createView();

        $this->assertSame('5.00', $view->vars['value']);
    }

    public function testBuildViewPrependsCurrencyToMoneyPatternWhenMissing(): void
    {
        $factory = $this->createFactory(['code' => 'USD']);
        $form = $factory->create(PriceType::class, null, ['currency' => 'USD']);
        $view = $form->createView();

        $this->assertStringContainsString('USD', $view->vars['money_pattern']);
    }

    public function testBuildViewDefaultsAttrWhenNoneGiven(): void
    {
        $factory = $this->createFactory(['code' => 'IDR']);
        $form = $factory->create(PriceType::class);
        $view = $form->createView();

        // Symfony form component adds inputmode => numeric, so only that is present
        $expectedAttr = ['inputmode' => 'numeric'];
        $this->assertSame($expectedAttr, $view->vars['attr']);
    }

    public function testBuildViewKeepsCustomAttr(): void
    {
        $factory = $this->createFactory(['code' => 'IDR']);
        $form = $factory->create(PriceType::class, null, ['attr' => ['class' => 'my-input']]);
        $view = $form->createView();

        // Custom attr replaces default, so no style is added
        $expectedAttr = ['class' => 'my-input', 'inputmode' => 'numeric'];
        $this->assertSame($expectedAttr, $view->vars['attr']);
    }

    public function testBuildViewExposesAutoCentFormatCurrencySeparatorsAndAllowNegative(): void
    {
        $factory = $this->createFactory(['code' => 'IDR', 'cent_point' => ',', 'thousand_point' => '.'], false);
        $form = $factory->create(PriceType::class, null, ['auto_cent_format' => false]);
        $view = $form->createView();

        $this->assertFalse($view->vars['auto_cent_format']);
        $this->assertFalse($view->vars['allow_negative']);
        $this->assertSame(',', $view->vars['cents_separator']);
        $this->assertSame('.', $view->vars['thousands_separator']);
        $this->assertSame('', $view->vars['prefix']);
        $this->assertSame('', $view->vars['suffix']);
    }
}
