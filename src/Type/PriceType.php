<?php

namespace Kematjaya\PriceBundle\Type;

use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
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
    /**
     * 
     * @var CurrencyFormatInterface
     */
    private $currencyFormat;

    public function __construct(CurrencyFormatInterface $currencyFormat)
    {
        $this->currencyFormat = $currencyFormat;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        
        $resolver->setDefaults([
            'invalid_message' => 'The selected issue does not exist',
            'currency' => $this->currencyFormat->getCurrencySymbol(),
            'prefix' => '',
            'suffix' => '',
            'cents-separator' => '.',
            'scale' => 0
        ]);
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($value) use ($options) {
                
                    if (0 == $options['scale']) {
                        
                        return $value;
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
                    
                    return $this->currencyFormat->priceToFloat($value, $options['scale']);
                }
            )
        );
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        
        if (false === strpos($view->vars['money_pattern'], $this->currencyFormat->getCurrencySymbol())) {
            $view->vars['money_pattern'] = sprintf("%s %s", $this->currencyFormat->getCurrencySymbol(), $view->vars['money_pattern']);
        }
        
        $view->vars['prefix'] = $options['prefix'];
        $view->vars['suffix'] = $options['suffix'];
        $view->vars['cents_separator'] = $options['cents-separator'];
        $view->vars['scale'] = isset($options['scale']) ? $options['scale'] : 0;
    }
}
