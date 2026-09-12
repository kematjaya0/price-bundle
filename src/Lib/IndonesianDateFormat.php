<?php

/**
 * This file is part of the kematjaya-currency-lib.
 */

declare(strict_types=1);

namespace Kematjaya\PriceBundle\Lib;

use DateTime;
use DateTimeInterface;

/**
 * @package Kematjaya\PriceBundle\Lib
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class IndonesianDateFormat extends AbstractDateFormat
{
    private const DAYS = [
        'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis',
        'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu',
    ];

    private const MONTHS = [
        'Jan' => 'Januari', 'Feb' => 'Februari', 'Mar' => 'Maret', 'Apr' => 'April',
        'May' => 'Mei', 'Jun' => 'Juni', 'Jul' => 'Juli', 'Aug' => 'Agustus',
        'Sep' => 'September', 'Oct' => 'Oktober', 'Nov' => 'November', 'Dec' => 'Desember',
    ];

    private const MONTH_NUMBERS = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
    ];

    public function getDayName(string $day): string
    {
        return self::DAYS[$day] ?? $day;
    }

    public function getMonthName(string $month): string
    {
        return self::MONTHS[$month] ?? $month;
    }

    public function getLabels(): array
    {
        return [
            'D' => 'Hari', 'd' => 'Tanggal', 'M' => 'Bulan', 'Y' => 'Tahun',
        ];
    }

    /**
     * @param string $dateString example: "05 Juli 2018,12:09"
     */
    public function reverse(string $dateString, string $splitTime = ','): ?DateTimeInterface
    {
        $dateAndTime = array_filter(explode($splitTime, trim($dateString)));
        if (empty($dateAndTime) || !isset($dateAndTime[0])) {
            return null;
        }

        $dateObject = $this->reverseDate($dateAndTime[0]);
        if (null === $dateObject) {
            return null;
        }

        $time = $dateAndTime[1] ?? null;
        if (!$time) {
            return $dateObject;
        }

        [$hour, $minute] = $this->reverseTime($time);
        $dateObject->setTime($hour, $minute);

        return $dateObject;
    }

    private function reverseDate(string $date): ?DateTime
    {
        $parts = explode(' ', $date);
        if (3 !== count($parts)) {
            return null;
        }

        [$day, $monthName, $year] = $parts;
        $monthNumbers = array_flip(self::MONTH_NUMBERS);
        if (!isset($monthNumbers[$monthName])) {
            return null;
        }

        return new DateTime(sprintf('%s-%s-%s', $year, $monthNumbers[$monthName], $day));
    }

    /**
     * @return array{0: int, 1: int} [hour, minute]
     */
    private function reverseTime(string $time): array
    {
        $timeParts = array_filter(explode(' ', trim($time)));
        $hourMinute = isset($timeParts[0]) ? explode(':', $timeParts[0]) : [];

        return [
            (int) ($hourMinute[0] ?? 0),
            (int) ($hourMinute[1] ?? 0),
        ];
    }
}
