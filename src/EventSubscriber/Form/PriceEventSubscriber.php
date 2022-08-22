<?php

namespace Kematjaya\PriceBundle\EventSubscriber\Form;

use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class PriceEventSubscriber implements EventSubscriberInterface
{
    /**
     * 
     * @var CurrencyFormatInterface
     */
    private $currencyFormat;
    
    /**
     * 
     * @var string
     */
    private $name;
    
    public function __construct(CurrencyFormatInterface $currencyFormat) 
    {
        $this->currencyFormat = $currencyFormat;
    }
    
    public function setName(string $name):self
    {
        $this->name = $name;
        
        return $this;
    }
    
    public static function getSubscribedEvents():array
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit'
        ];
    }
    
    public function preSubmit(FormEvent $event):void
    {
        $data = $event->getData();
        if ($this->name and isset($data[$this->name])) {
            $data[$this->name] = $data[$this->name] ? (float) $this->currencyFormat->priceToFloat($data[$this->name]): 0;
            $event->setData($data);
        }   
    }
}
