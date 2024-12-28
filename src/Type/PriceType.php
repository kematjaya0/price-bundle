<?php

namespace Kematjaya\PriceBundle\Type;

use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class PriceType extends MoneyType
{
    private CurrencyFormatInterface $currencyFormat;

    private array $configs;

    public function __construct(CurrencyFormatInterface $currencyFormat, ParameterBagInterface $bag)
    {
        $this->configs = $bag->get("price")["currency"];

        $this->currencyFormat = $currencyFormat;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            "auto_cent_format" => true,
            'invalid_message' => 'The selected issue does not exist',
            'currency' => $this->currencyFormat->getCurrencySymbol(),
            'prefix' => $this->currencyFormat->getCurrencySymbol(),
            'suffix' => '',
            'cents-separator' => $this->currencyFormat->getCentPoint(),
            'thousands-separator' => $this->currencyFormat->getThousandPoint(),
            'scale' => (int)$this->currencyFormat->getCentLimit()
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($value) use ($options) {

                    if (0 == $options['scale']) {

                        return round($value);
                    }

                    $values = explode(".", $value);
                    $values[0] = 0 !== strlen($values[0]) ? $values[0]: 0;
                    $values[1] = isset($values[1]) ? $values[1]: 0;
                    for ($i = strlen($values[1]); $i < $options['scale']; $i++) {
                        $values[1] .= '0';
                    }

                    return implode(".", $values);
                }, function (?string $value) use ($options) {
                if (null === $value) {

                    return 0;
                }

                return $this->currencyFormat->priceToFloat($value, $options['currency'], $options["scale"]);
            }
            )
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options):void
    {
        parent::buildView($view, $form, $options);

        if (false === strpos($view->vars['money_pattern'], $options["currency"])) {
            $view->vars['money_pattern'] = sprintf("%s %s", $options["currency"], $view->vars['money_pattern']);
        }

        $view->vars["attr"] = !empty($view->vars['attr']) ? $view->vars['attr'] : ["style" => 'text-align: right'];
        $view->vars['prefix'] = "";
        $view->vars['suffix'] = "";
        $view->vars["auto_cent_format"] = $options["auto_cent_format"];
        $view->vars['allow_negative'] = $this->configs["allow_negative"];
        $view->vars['cents_separator'] = $options['cents-separator'];
        $view->vars['thousands_separator'] = $options['thousands-separator'];
        $view->vars['scale'] = isset($options['scale']) ? $options['scale'] : 0;
    }
}
