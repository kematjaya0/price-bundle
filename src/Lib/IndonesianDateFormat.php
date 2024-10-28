<?php

namespace Kematjaya\PriceBundle\Lib;

class IndonesianDateFormat extends AbstractDateFormat
{
    /**
     * 
     * @param string $day
     * @return string
     */
    public function getDayName(string $day):string
    {
        $date = ['Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu'];
        
        return (isset($date[$day])) ? $date[$day] : $day;
    }
    
    /**
     * 
     * @param string $month
     * @return string
     */
    public function getMonthName(string $month):string
    {
        $bulan = [
            'Jan' => 'Januari', 'Feb' => 'Februari', 'Mar' => 'Maret',  'Apr' => 'April',
            'May' => 'Mei',     'Jun' => 'Juni',     'Jul' => 'Juli',   'Aug' => 'Agustus',
            'Sep' => 'September', 'Oct' => 'Oktober', 'Nov' => 'November', 'Dec' => 'Desember'
        ];
        
        return (isset($bulan[$month])) ? $bulan[$month] : $month;
    }
    
    public function getLabels():array
    {
        return [
            'D' => 'Hari', 'd' => 'Tanggal', 'M' => 'Bulan', 'Y' => 'Tahun'
        ];
    }
    
    /**
     * 
     * @param string example: 05 Juli 2018,12:09
     * @param string default ","
     * @return \DateTimeInterface|null
     */
    public function reverse(string $dateString, string $splitTime = ','):?\DateTimeInterface
    {
        $monthList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',  '04' => 'April',
            '05' => 'Mei',     '06' => 'Juni',     '07' => 'Juli',   '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        $listMonths = array_flip($monthList);
        
        $dateArr = array_filter(
                explode($splitTime, trim($dateString))
        );
        if (empty($dateArr)) {
            
            return null;
        }
        
        $date = isset($dateArr[0]) ? $dateArr[0] : null;
        if (!$date) {
            
            return null;
        }
        
        list($day, $bulan, $year) = explode(" ", $date);
        $month = isset($listMonths[$bulan]) ? $listMonths[$bulan] : null;
        if (null === $month) {
            
            return null;
        }
        
        $dateObject = new \DateTime(sprintf("%s-%s-%s", $year, $month, $day));
        $time = isset($dateArr[1]) ? $dateArr[1] : null;
        if (!$time) {
            
            return $dateObject;
        }
        
        $timeArr = array_filter(
            explode(" ", trim($time))
        );
        $time = isset($timeArr[0]) ? explode(":", $timeArr[0]) : 0;
        $dateObject->setTime(
            isset($time[0]) ? $time[0] : 0, 
            isset($time[1]) ? $time[1] : 0
        );
        
        return $dateObject;
    }
}
