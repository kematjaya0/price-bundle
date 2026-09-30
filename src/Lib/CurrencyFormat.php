<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Lib;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Intl\Currencies;

class CurrencyFormat implements CurrencyFormatInterface
{
    /**
     * @var int
     */
    private $centLimit;

    /**
     * @var string
     */
    private $centPoint;

    /**
     * @var string
     */
    private $thousandPoint;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var array<string, int>
     */
    private $centLimits;

    public function __construct(ContainerBagInterface $container)
    {
        $configs = $container->get('price');
        $this->currency = $configs['currency']['code'];
        $this->centLimit = (int) $configs['currency']['cent_limit'];
        $this->centPoint = $configs['currency']['cent_point'];
        $this->thousandPoint = $configs['currency']['thousand_point'];
        $this->centLimits = $configs['currency']['cent_limits'] ?? [];

        $this->isValid($this->currency);
    }

    public function getCurrencySymbol(): ?string
    {
        return Currencies::getSymbol($this->currency);
    }

    public function setCentLimit(int $centLimit): self
    {
        $this->centLimit = $centLimit;

        return $this;
    }

    public function getCentPoint(): string
    {
        return $this->centPoint;
    }

    public function getCentLimit(): int
    {
        return (int) $this->centLimit;
    }

    public function getCentLimitByCurrency(string $currency): int
    {
        return isset($this->centLimits[$currency]) ? (int) $this->centLimits[$currency] : $this->centLimit;
    }

    public function getCentLimits(): array
    {
        return $this->centLimits;
    }

    public function getThousandPoint(): string
    {
        return $this->thousandPoint;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->isValid($currency);

        $this->currency = $currency;

        return $this;
    }

    public function priceToFloat(
        string $price = '0',
        ?string $currency = null,
        ?int $centLimit = null,
        ?string $centPoint = null,
        ?string $thousandPoint = null
    ): float {
        $currency = $currency ?? $this->currency;
        try {
            $this->isValid($currency);
            $symbol = Currencies::getSymbol($currency);
        } catch (\InvalidArgumentException $e) {
            $symbol = $currency;
        }

        $numbers = explode($centPoint ?? $this->getCentPoint(), str_replace($symbol, '', $price));
        $numbers[0] = str_replace($thousandPoint ?? $this->getThousandPoint(), '', trim($numbers[0]));

        return round((float) implode('.', $numbers), $centLimit ?? $this->getCentLimitByCurrency($currency));
    }

    public function formatPrice(
        float $number = 0,
        ?int $centLimit = null,
        ?string $centPoint = null,
        ?string $thousandPoint = null,
        ?string $currency = null
    ): string {
        $currencyCode = $currency ?? $this->currency;
        $this->isValid($currencyCode);
        $symbol = Currencies::getSymbol($currencyCode);

        return sprintf(
            '%s %s',
            $symbol,
            number_format(
                $number,
                $centLimit ?? $this->getCentLimitByCurrency($currencyCode),
                $centPoint ?? $this->getCentPoint(),
                $thousandPoint ?? $this->getThousandPoint()
            )
        );
    }

    protected function isValid(string $currency): bool
    {
        $names = Currencies::getNames();
        if (!isset($names[$currency])) {
            throw new \InvalidArgumentException(sprintf('%s not supported', $currency));
        }

        return true;
    }
}