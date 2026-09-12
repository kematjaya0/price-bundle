<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Tests;

use Kematjaya\PriceBundle\PriceBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PriceBundleTest extends TestCase
{
    public function testIsASymfonyBundle(): void
    {
        $this->assertInstanceOf(Bundle::class, new PriceBundle());
    }
}
