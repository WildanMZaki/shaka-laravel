<?php

namespace App\Helpers;

use DateTime;

trait Formatter
{
    public static $idDays = [
        'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
    ];
    public static $idMonths = [
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
    ];
    public static $idLongMonths = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    /** 
     * @param string date YYYY-mm-dd HH:ii:ss
     * @return string Hari ini, HH:ii - Kemarin - HH:ii, Minggu, 15 Agu 2004
     */
    public static function passDate(string $timestamp): string
    {
        // Convert the input string to a DateTime object
        $inputedDate = new DateTime($timestamp);
        $now = new DateTime();

        // Check if the date is today
        if ($inputedDate->format('Y-m-d') === $now->format('Y-m-d')) {
            return 'Hari ini, ' . $inputedDate->format('H:i');
        }

        // Check if the inputedDate is yesterday
        $yesterday = clone $now;
        $yesterday->modify('-1 day');
        if ($inputedDate->format('Y-m-d') === $yesterday->format('Y-m-d')) {
            return 'Kemarin, ' . $inputedDate->format('H:i');
        }

        // Return something like Minggu, 15 Agu 2005
        $formattedDate = self::$idDays[$inputedDate->format('w')] . ', ';
        $formattedDate .= $inputedDate->format('j ');
        $formattedDate .= self::$idMonths[$inputedDate->format('n') - 1];
        $formattedDate .= $inputedDate->format(' Y');
        return $formattedDate;
    }

    public static function today()
    {
        $today = now();
        $formattedDate = self::$idDays[$today->dayOfWeek] . ', '
            . $today->day . ' ' . self::$idMonths[$today->month - 1] . ' ' . $today->year;
        return $formattedDate;
    }

    public static function longDate(string $inputDate): string
    {
        $date = new DateTime($inputDate);
        $formattedDate = self::$idDays[$date->format('w')] . ', ' . // Menggunakan 'w' untuk mendapatkan hari dalam format numerik (0-6)
            $date->format('j') . ' ' . self::$idMonths[$date->format('n') - 1] . ' ' . $date->format('Y');
        return $formattedDate;
    }

    /**
     * Digunakan untuk memformat opsi dari array of item menjadi format yang cocok untuk input select dengan select2
     */
    public static function select2Data($data, $text)
    {
        $result = [];
        foreach ($data as $i => $item) {
            $result[] = (object)[
                'id' => $item->id,
                'text' => $item->$text,
            ];
        }
        return json_encode($result);
    }

    public static function seo($string): string
    {
        $seoname = str_replace(' ', '-', $string);
        // Remove special characters
        $seoname = preg_replace('/[^A-Za-z0-9\-]/', '', $seoname);
        $seoname = strtolower($seoname);

        return $seoname;
    }

    /**
     * @return string Ex: Rp 100.000
     */
    public static function rupiah(int $nominal): string
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }

    public static function ribuan(int $num): string
    {
        return number_format($num, 0, ',', '.');
    }

    public static function validInt(string $stringInt): int
    {
        // Remove non-numeric characters, except for dots and commas
        $numericString = preg_replace('/[^0-9.,]/', '', $stringInt);

        // Replace commas with dots for consistent decimal handling
        $numericString = str_replace(',', '.', $numericString);

        // Cast the string to an integer (it will be converted to the nearest integer)
        $intValue = (int) $numericString;

        return $intValue;
    }

    public static function nominalRupiah(string $stringRupiah): int
    {
        $sanitized_str = preg_replace('/[^0-9]/', '', $stringRupiah);
        $amount_int = (int)$sanitized_str;
        return $amount_int;
    }

    public static function simpleDate(string $dateTime): string
    {
        return date('d M Y', strtotime($dateTime));
    }

    public static function ceilToHundreds($value)
    {
        $value = abs($value);
        $roundedValue = ceil($value / 100) * 100;
        return $roundedValue;
    }

    public static function greetingTime($time)
    {
        $hour = (int) date('H', strtotime($time));

        if ($hour >= 5 && $hour < 11) {
            return "pagi";
        } elseif ($hour >= 11 && $hour < 15) {
            return "siang";
        } elseif ($hour >= 15 && $hour < 19) {
            return "sore";
        } else {
            return "malam";
        }
    }

    /**
     * @param string|null $datetime in format YYYY-mm-dd
     * @return string first monday from the date time
     */
    public static function firstMonday(?string $datetime = null): string
    {
        $datetime = $datetime ?? date('Y-m-d');
        $currentDayOfWeek = date('N', strtotime($datetime));

        if ($currentDayOfWeek == 1) {
            $firstMonday = $datetime . ' 00:00:00';
        } else {
            $firstMonday = date('Y-m-d', strtotime('last Monday', strtotime($datetime))) . ' 00:00:00';
        }

        return $firstMonday;
    }

    public static function mondayUntilNow(): array
    {
        $start = self::firstMonday();
        $end = date('Y-m-d 23:59:59');
        return [$start, $end];
    }

    public static function nextMondayFrom($monday): string
    {
        $currentMondayObj = DateTime::createFromFormat('Y-m-d', $monday);
        $nextMondayObj = $currentMondayObj->modify('+7 days');
        return $nextMondayObj->format('Y-m-d');
    }

    public static function convertPeriod($period)
    {
        list($start, $end) = explode(' - ', $period);

        $startDate = new DateTime($start);
        $endDate = new DateTime($end);

        if ($startDate->format('Y-m') === $endDate->format('Y-m')) {
            return $startDate->format('d') . ' - ' . $endDate->format('d M');
        } else {
            return $startDate->format('d M') . ' - ' . $endDate->format('d M');
        }
    }

    public static function onlyDate(string $dateTime): string
    {
        return date('Y-m-d', strtotime($dateTime));
    }

    public static function convertPeriodLitleLong($period)
    {
        list($start, $end) = explode(' - ', $period);

        $startDate = new DateTime($start);
        $endDate = new DateTime($end);

        if ($startDate->format('Y') === $endDate->format('Y')) {
            return $startDate->format('d M') . ' - ' . $endDate->format('d M Y');
        } else {
            return $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        }
    }

    public static function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = self::penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = self::penyebut($nilai / 10) . " puluh" . self::penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . self::penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = self::penyebut($nilai / 100) . " ratus" . self::penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . self::penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = self::penyebut($nilai / 1000) . " ribu" . self::penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = self::penyebut($nilai / 1000000) . " juta" . self::penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = self::penyebut($nilai / 1000000000) . " milyar" . self::penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = self::penyebut($nilai / 1000000000000) . " trilyun" . self::penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    public static function terbilang($nilai)
    {
        if ($nilai < 0) {
            $hasil = "minus " . trim(self::penyebut($nilai));
        } else {
            $hasil = trim(self::penyebut($nilai));
        }
        return ucwords($hasil) . ' Rupiah';
    }

    public static function oneMonthSince(string $dateString)
    {
        $nextMonthDate = date('Y-m-d', strtotime($dateString . ' +1 month'));
        $resultDate = date('Y-m-d', strtotime($nextMonthDate . ' -1 day'));
        return $resultDate;
    }
}
