<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\PriceBundle\Tests\Type;

/**
 * Description of TestFormModel
 *
 * @author guest
 */
class TestFormModel 
{
    /**
     * 
     * @var float
     */
    private $nilai;
    
    public function getNilai(): ?float 
    {
        return $this->nilai;
    }

    public function setNilai(float $nilai): self 
    {
        $this->nilai = $nilai;
        
        return $this;
    }


    
}
