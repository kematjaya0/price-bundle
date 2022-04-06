<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\PriceBundle\Tests\Type;

use Kematjaya\PriceBundle\Tests\Type\TestFormModel;
use Kematjaya\PriceBundle\Tests\Type\TestFormType;
use Kematjaya\PriceBundle\Lib\CurrencyFormat;
use Kematjaya\PriceBundle\Type\PriceType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * Description of PriceTypeTest
 *
 * @author guest
 */
class PriceTypeTest extends TypeTestCase 
{
    private $currencyFormatMock;
    
    public function setUp(): void 
    {
        $configs = [
            "currency" => [
                "code" => "IDR",
                "cent_limit" => 2,
                "cent_point" => ",",
                "thousand_point" => "."
              ]
        ];
        $container = $this->createMock(ContainerBagInterface::class);
        $container->method("get")
                ->with("price")
                ->willReturn($configs);
        $this->currencyFormatMock = new CurrencyFormat($container);
        
        parent::setUp();
    }
    
    protected function getExtensions(): array
    {
        $priceType = new PriceType($this->currencyFormatMock);
        return [
            new PreloadedExtension([$priceType], []),
        ];
    }
    
    public function testSubmitValidData(): void
    {
        $data = [
            "IDR 20.300,02" => (float)20300.02,
            "IDR 20.000,02" => (float)20000.02,
            "IDR 20.300,52" => (float)20300.52
        ];
        
        foreach ($data as $nilai => $expected) {
            $formData = [
                'nilai' => $nilai
            ];
            $data = new TestFormModel();
            $form = $this->factory->create(TestFormType::class, $data);
            $form->submit($formData);
            self::assertTrue($form->isSynchronized());
            self::assertEquals($expected, $data->getNilai());

            $view     = $form->createView();
            $children = $view->children;

            foreach (array_keys($formData) as $key) {
                self::assertArrayHasKey($key, $children);
            }
        }
            
    }
}
