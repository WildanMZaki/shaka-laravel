<?php

namespace App\Helpers;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WzExcel implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $title;
    protected $headings;
    protected $data;

    public function __construct(string $title, array $headings, array $data)
    {
        $this->title = $title;
        $this->headings = $headings;
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = count($this->data) + 1; // Jumlah baris + baris judul

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'font' => [
                'bold' => true,
            ],
        ];

        $headerRange = "A1:" . $sheet->getHighestColumn() . "1"; // Range untuk header
        $dataRange = "A2:" . $sheet->getHighestColumn() . $totalRows; // Range untuk data

        // Menerapkan gaya pada header
        $sheet->getStyle($headerRange)->applyFromArray($styleArray);

        // Menerapkan gaya pada data
        $sheet->getStyle($dataRange)->applyFromArray(['borders' => $styleArray['borders']]);
    }
}
