<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Type;

use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class PriceType extends MoneyType
{
    /**
     * @var CurrencyFormatInterface
     */
    private $currencyFormat;

    /**
     * @var array<string, mixed>
     */
    private $configs;

    public function __construct(CurrencyFormatInterface $currencyFormat, ParameterBagInterface $bag)
    {
        $this->configs = $bag->get('price')['currency'];
        $this->currencyFormat = $currencyFormat;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'auto_cent_format' => true,
            'invalid_message' => 'The selected issue does not exist',
            'currency' => $this->currencyFormat->getCurrency(),
            'prefix' => $this->currencyFormat->getCurrencySymbol(),
            'suffix' => '',
            'cents-separator' => $this->currencyFormat->getCentPoint(),
            'thousands-separator' => $this->currencyFormat->getThousandPoint(),
        ]);

        // Lazily derived from the resolved "currency" option, so a field's
        // decimal precision follows config/price.yaml's per-currency
        // cent_limits map (e.g. USD => 2) unless a caller explicitly passes
        // "scale" itself.
        $resolver->setDefault('scale', function (Options $options): int {
            return $this->currencyFormat->getCentLimitByCurrency($options['currency']);
        });
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $scale = (int) $options['scale'];
        $currency = $options['currency'];

        $builder->addModelTransformer(new CallbackTransformer(
            function ($value) use ($scale) {
                return $this->padToScale($value, $scale);
            },
            function (?string $value) use ($currency, $scale): float {
                return $this->parseToFloat($value, $currency, $scale);
            }
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        if (false === strpos($view->vars['money_pattern'], $options['currency'])) {
            $view->vars['money_pattern'] = sprintf('%s %s', $options['currency'], $view->vars['money_pattern']);
        }

        $view->vars['attr'] = !empty($view->vars['attr']) ? $view->vars['attr'] : ['style' => 'text-align: right'];
        $view->vars['prefix'] = '';
        $view->vars['suffix'] = '';
        $view->vars['auto_cent_format'] = $options['auto_cent_format'];
        $view->vars['allow_negative'] = $this->configs['allow_negative'];
        $view->vars['cents_separator'] = $options['cents-separator'];
        $view->vars['thousands_separator'] = $options['thousands-separator'];
        $view->vars['scale'] = $options['scale'] ?? 0;
    }

    /**
     * Model to view: pads the fractional part of $value with trailing zeros
     * until it has $scale digits (never truncates an already-longer one).
     *
     * @param mixed $value
     *
     * @return float|string
     */
    private function padToScale($value, int $scale)
    {
        if (0 === $scale) {
            return round((float) $value);
        }

        $parts = explode('.', (string) $value);
        $whole = '' !== $parts[0] ? $parts[0] : '0';
        $fraction = str_pad($parts[1] ?? '', $scale, '0');

        return $whole . '.' . $fraction;
    }

    /**
     * View to model: parses the submitted, formatted string back into a
     * float using the field's currency and scale.
     */
    private function parseToFloat(?string $value, string $currency, int $scale): float
    {
        if (null === $value) {
            return 0.0;
        }

        return $this->currencyFormat->priceToFloat($value, $currency, $scale);
    }
}