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
}
