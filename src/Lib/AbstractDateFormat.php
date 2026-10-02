<?php

/**
 * This file is part of the kematjaya/price-bundle.
 */

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Lib;

use DateTimeInterface;
use Kematjaya\PriceBundle\Converter\ConverterInterface;

abstract class AbstractDateFormat implements DateFormatInterface
{
    public function __construct(protected readonly ConverterInterface $converter) {}

    abstract public function getDayName(string $day): string;

    abstract public function getMonthName(string $month): string;

    abstract public function reverse(string $date, string $splitTime = ','): ?DateTimeInterface;

    abstract public function getLabels(): array;

    public function format(DateTimeInterface $date, string $format = 'd M Y'): string
    {
        $prefix = $this->getPrefix($format);
        if (null === $prefix) {
            return $date->format($format);
        }

        return implode($prefix, $this->doFormat($date, $format, $prefix));
    }

    /**
     * Splits $format on its separator, translates the "D" (day name) and "M"
     * (month name) tokens, and formats every other token as-is.
     */
    protected function doFormat(DateTimeInterface $date, string $format, string $prefix): array
    {
        $result = [];
        foreach (explode($prefix, $format) as $value) {
            $result[$value] = match ($value) {
                'D' => $this->getDayName($date->format('D')),
                'M' => $this->getMonthName($date->format('M')),
                default => $date->format($value),
            };
        }

        return $result;
    }

    public function convertToString(DateTimeInterface $date, string $format = 'd M Y'): string
    {
        $prefix = $this->getPrefix($format);
        if (null === $prefix) {
            return $date->format($format);
        }

        $labels = $this->getLabels();
        $formats = $this->doFormat($date, $format, $prefix);
        foreach ($formats as $key => $value) {
            if (is_numeric($value)) {
                $formats[$key] = $this->converter->convert((float) $value);
            }
        }

        foreach ($formats as $key => $value) {
            $label = $labels[$key] ?? '';
            $formats[$key] = sprintf('%s %s', trim($label), trim($value));
        }

        return implode($prefix, $formats);
    }

    protected function getPrefix(string $format): ?string
    {
        $matched = null;
        foreach ($this->prefix() as $candidate) {
            if (str_contains($format, $candidate)) {
                $matched = $candidate;
            }
        }

        return $matched;
    }

    protected function prefix(): array
    {
        return ['/', '-', ' '];
    }
}
