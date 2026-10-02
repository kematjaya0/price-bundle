<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\EventSubscriber\Form;

use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PriceEventSubscriber implements EventSubscriberInterface
{
    private ?string $name = null;

    public function __construct(private readonly CurrencyFormatInterface $currencyFormat) {}

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        if (null === $this->name || !isset($data[$this->name])) {
            return;
        }

        $data[$this->name] = $data[$this->name] ? $this->currencyFormat->priceToFloat($data[$this->name]) : 0;
        $event->setData($data);
    }
}
