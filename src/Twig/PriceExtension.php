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
     * @var array<string, mixed>
     */
    private readonly array $configs;

    public function __construct(
        private readonly CurrencyFormatInterface $currencyFormat,
        ParameterBagInterface $bag,
        private readonly Environment $twig,
    ) {
        $this->configs = $bag->get('price')['currency'];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_price_javascript', fn(): string => $this->twig->render('@Price/javascripts.twig'), ['is_safe' => ['html']]),
            new TwigFunction('currency', $this->currency(...), ['is_safe' => ['html']]),
            new TwigFunction('price', $this->price(...), ['is_safe' => ['html']]),
            new TwigFunction('price_symbol', fn(): ?string => $this->currencyFormat->getCurrencySymbol()),
            new TwigFunction('thousand_point', fn(): string => $this->currencyFormat->getThousandPoint()),
            new TwigFunction('cent_point', fn(): string => $this->currencyFormat->getCentPoint()),
            new TwigFunction('cent_limit', fn(): int => $this->currencyFormat->getCentLimit()),
            new TwigFunction('allow_negative', fn(): bool => $this->configs['allow_negative']),
        ];
    }

    public function currency(
        mixed $number = 0,
        ?string $currency = null,
        ?int $centLimit = null,
        ?string $centPoint = null,
        ?string $thousandPoint = null
    ): ?string {
        return $this->price($number, $centLimit, $centPoint, $thousandPoint, $currency);
    }

    public function price(
        mixed $number = 0,
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
