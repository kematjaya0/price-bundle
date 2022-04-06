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
        $this->currencyFormatMock = $this->createConfiguredMock(CurrencyFormat::class, [
            "getCurrencySymbol" => "IDR"
        ]);
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
        $nilai = "IDR 20000";
        $expected = (float)20000;
        $this->currencyFormatMock
                ->method("priceToFloat")
                ->with($nilai)
                ->willReturn($expected);
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
