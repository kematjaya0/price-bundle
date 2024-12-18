<?php

namespace Kematjaya\PriceBundle\Lib;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Intl\Currencies;
use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;

class CurrencyFormat implements CurrencyFormatInterface
{
    /**
     *
     * @var float
     */
    private $centLimit = 0;
    
    /**
     *
     * @var string
     */
    private $centPoint = '.';
    
    /**
     *
     * @var string
     */
    private $thousandPoint = ',';
    
    /**
     *
     * @var string
     */
    private $currency;
    
    function __construct(ContainerBagInterface $container) 
    {
        $configs            = $container->get('price');
        $this->currency     = $configs['currency']['code'];
        $this->centLimit    = $configs['currency']['cent_limit'];
        $this->centPoint        = $configs['currency']['cent_point'];
        $this->thousandPoint    = $configs['currency']['thousand_point'];
        
        $this->isValid($this->currency);
    }
    
    /**
     * 
     * @param type string currency code ex: "IDR", "USD"
     * @return string|null
     */
    public function getCurrencySymbol():?string
    {
        return Currencies::getSymbol($this->currency);
    }
    
    public function setCentLimit(string $centLimit):self
    {
        $this->centLimit = $centLimit;
        
        return $this;
    }
    
    public function getCentPoint(): string 
    {
        return $this->centPoint;
    }

    public function getCentLimit():string
    {
        return $this->centLimit;
    }
    
    public function getThousandPoint(): string 
    {
        return $this->thousandPoint;
    }

    public function getCurrency(): string 
    {
        return $this->currency;
    }

        
    public function setCurrency(string $currency):self
    {
        $this->isValid($currency);
        
        $this->currency = $currency;
        
        return $this;
    }
    
    public function priceToFloat(string $price = '0', string $currency = null):float
    {
        $currency = null !== $currency ? $currency : $this->getCurrencySymbol();
        $this->isValid($currency);

        $numbers = explode($this->getCentPoint(), str_replace($currency, '', $price));
        $numbers[0] = str_replace($this->getThousandPoint(), '', trim($numbers[0]));
        
        return round((float) implode(".", $numbers), $this->getCentLimit());
    }
    
    public function formatPrice(float $number = 0, int $centLimit = null, string $centPoint = null, string $thousandPoint = null, string $currency = null):string
    {
        $currencyCode = null !== $currency ? $currency : $this->getCurrencySymbol();
        $this->isValid($currencyCode);

        return sprintf("%s %s", $currencyCode,  number_format($number, null !== $centLimit ? $centLimit : $this->getCentLimit(), null !== $centPoint ? $centPoint : $this->getCentPoint(), null !== $thousandPoint ? $thousandPoint : $this->getThousandPoint()));
    }
    
    protected function isValid(string $currency):bool
    {
        $names = Currencies::getNames();
        if (!isset($names[$currency])) {
            throw new \Exception(sprintf("%s not supported", $currency));
        }
        
        return true;
    }
}
