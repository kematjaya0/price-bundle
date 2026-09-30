<?php

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Tests\Twig;

use Kematjaya\PriceBundle\Converter\IndonesianConverter;
use Kematjaya\PriceBundle\Lib\IndonesianDateFormat;
use Kematjaya\PriceBundle\Twig\DateFormatExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;

class DateFormatExtensionTest extends TestCase
{
    public function testGetFiltersRegistersDateFormatAndDateToString(): void
    {
        $dateFormat = new IndonesianDateFormat(new IndonesianConverter());
        $extension = new DateFormatExtension($dateFormat);

        $filters = $extension->getFilters();

        $this->assertCount(2, $filters);
        $this->assertContainsOnlyInstancesOf(TwigFilter::class, $filters);
        $this->assertSame('date_format', $filters[0]->getName());
        $this->assertSame([$dateFormat, 'format'], $filters[0]->getCallable());
        $this->assertSame('date_to_string', $filters[1]->getName());
        $this->assertSame([$dateFormat, 'convertToString'], $filters[1]->getCallable());
    }
}
