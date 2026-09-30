<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Tests\EventSubscriber\Form;

use Kematjaya\PriceBundle\EventSubscriber\Form\PriceEventSubscriber;
use Kematjaya\PriceBundle\Lib\CurrencyFormatInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class PriceEventSubscriberTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $this->assertSame(
            [FormEvents::PRE_SUBMIT => 'preSubmit'],
            PriceEventSubscriber::getSubscribedEvents()
        );
    }

    public function testSetNameIsFluent(): void
    {
        $subscriber = new PriceEventSubscriber($this->createMock(CurrencyFormatInterface::class));

        $this->assertSame($subscriber, $subscriber->setName('price'));
    }

    public function testPreSubmitDoesNothingWhenNameWasNeverSet(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->expects($this->never())->method('priceToFloat');

        $subscriber = new PriceEventSubscriber($currencyFormat);
        $event = $this->createEvent(['price' => '1.000']);

        $subscriber->preSubmit($event);

        $this->assertSame(['price' => '1.000'], $event->getData());
    }

    public function testPreSubmitDoesNothingWhenFieldIsMissingFromData(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->expects($this->never())->method('priceToFloat');

        $subscriber = (new PriceEventSubscriber($currencyFormat))->setName('price');
        $event = $this->createEvent(['other' => '1.000']);

        $subscriber->preSubmit($event);

        $this->assertSame(['other' => '1.000'], $event->getData());
    }

    public function testPreSubmitConvertsFalsyValueToZero(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->expects($this->never())->method('priceToFloat');

        $subscriber = (new PriceEventSubscriber($currencyFormat))->setName('price');
        $event = $this->createEvent(['price' => '']);

        $subscriber->preSubmit($event);

        $this->assertSame(['price' => 0], $event->getData());
    }

    public function testPreSubmitConvertsFormattedPriceToFloat(): void
    {
        $currencyFormat = $this->createMock(CurrencyFormatInterface::class);
        $currencyFormat->expects($this->once())
            ->method('priceToFloat')
            ->with('1.000')
            ->willReturn(1000.0);

        $subscriber = (new PriceEventSubscriber($currencyFormat))->setName('price');
        $event = $this->createEvent(['price' => '1.000']);

        $subscriber->preSubmit($event);

        $this->assertSame(['price' => 1000.0], $event->getData());
    }

    private function createEvent(array $data): FormEvent
    {
        return new FormEvent($this->createMock(FormInterface::class), $data);
    }
}
