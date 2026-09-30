<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Twig;

use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class PriceExtension extends AbstractExtension
{
    /**
     * @var CurrencyFormatInterface
     */
    private $currencyFormat;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var array<string, mixed>
     */
    private $configs;

    public function __construct(CurrencyFormatInterface $currencyFormat, ParameterBagInterface $bag, Environment $twig)
    {
        $this->configs = $bag->get('price')['currency'];
        $this->currencyFormat = $currencyFormat;
        $this->twig = $twig;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_price_javascript', function (): string {
                return $this->twig->render('@Price/javascripts.twig');
            }, ['is_safe' => ['html']]),
            new TwigFunction('currency', [$this, 'currency'], ['is_safe' => ['html']]),
            new TwigFunction('price', [$this, 'price'], ['is_safe' => ['html']]),
            new TwigFunction('price_symbol', function (): ?string {
                return $this->currencyFormat->getCurrencySymbol();
            }),
            new TwigFunction('thousand_point', function (): string {
                return $this->currencyFormat->getThousandPoint();
            }),
            new TwigFunction('cent_point', function (): string {
                return $this->currencyFormat->getCentPoint();
            }),
            new TwigFunction('cent_limit', function (): int {
                return $this->currencyFormat->getCentLimit();
            }),
            new TwigFunction('allow_negative', function (): bool {
                return $this->configs['allow_negative'];
            }),
        ];
    }

    /**
     * @param mixed $number
     */
    public function currency(
        $number = 0,
        ?string $currency = null,
        ?int $centLimit = null,
        ?string $centPoint = null,
        ?string $thousandPoint = null
    ): ?string {
        return $this->price($number, $centLimit, $centPoint, $thousandPoint, $currency);
    }

    /**
     * @param mixed $number
     */
    public function price(
        $number = 0,
        ?int $centLimit = null,
        ?string $centPoint = null,
        ?string $thousandPoint = null,
        ?string $currency = null
    ): ?string {
        if (!is_numeric($number)) {
            $number = 0;
        }

        return $this->currencyFormat->formatPrice((float) $number, $centLimit, $centPoint, $thousandPoint, $currency);
    }
}