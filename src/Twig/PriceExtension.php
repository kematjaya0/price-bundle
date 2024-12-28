<?php

namespace Kematjaya\PriceBundle\Twig;

use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class PriceExtension extends AbstractExtension
{

    private CurrencyFormatInterface $currencyFormat;
    private Environment $twig;
    private array $configs;

    public function __construct(CurrencyFormatInterface $currencyFormat, ParameterBagInterface $bag, Environment $twig)
    {
        $this->configs = $bag->get("price")["currency"];
        $this->currencyFormat = $currencyFormat;
        $this->twig = $twig;
    }

    public function getFunctions():array
    {
        return array(
            new TwigFunction("render_price_javascript", function () {
                return $this->twig->render("@Price/javascripts.twig");
            }, array('is_safe' => array('html'))),
            new TwigFunction('currency', array($this, 'currency'), array('is_safe' => array('html'))),
            new TwigFunction('price', array($this, 'price'), array('is_safe' => array('html'))),
            new TwigFunction('price_symbol', function () {
                return $this->currencyFormat->getCurrencySymbol();
            }),
            new TwigFunction('thousand_point', function () {
                return $this->currencyFormat->getThousandPoint();
            }),
            new TwigFunction('cent_point', function () {
                return $this->currencyFormat->getCentPoint();
            }),
            new TwigFunction('cent_limit', function () {
                return $this->currencyFormat->getCentLimit();
            }),
            new TwigFunction('allow_negative', function () {
                return $this->configs["allow_negative"];
            })
        );
    }

    public function currency($number = 0, string $currency = null, int $centLimit = null, string $centPoint = null, string $thousandPoint = null):?string
    {
        if (!is_numeric($number)) {
            $number = 0;
        }

        return $this->currencyFormat->formatPrice($number, $centLimit, $centPoint, $thousandPoint, $currency);
    }

    public function price($number = 0, int $centLimit = null, string $centPoint = null, string $thousandPoint = null, string $currency = null):?string
    {
        if (!is_numeric($number)) {
            $number = 0;
        }

        return $this->currencyFormat->formatPrice($number, $centLimit, $centPoint, $thousandPoint, $currency);
    }
}
