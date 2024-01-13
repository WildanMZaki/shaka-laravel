<?php

namespace App\Helpers;

trait Formatter
{
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
}
