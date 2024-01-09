<?php

namespace App\Helpers;

/**
 * Just a litle not, Muwiza is stand for my name: Wildan Muhammad Zaki, Mu-wi-za: hehe
 */
class Muwiza
{
    use Formatter;

    public static function dataTable($rowsData, callable $callback)
    {
        $rows = [];
        foreach ($rowsData as $rowData) {
            $rows[] = $callback($rowData);
        }
        return $rows;
    }
}
