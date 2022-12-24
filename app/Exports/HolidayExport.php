<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class HolidayExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    /**
     * Constructor
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Collection of data
     */
    public function collection()
    {
        return collect($this->data);
    }

    /**
     * CSV Header
     */
    public function headings(): array
    {
        return [
            trans('app.date'),
            trans('app.holiday_name')
        ];
    }

    /**
     * CSV content
     */
    public function map($holiday): array
    {
        return [
            Carbon::parse($holiday->date)->format('Y/m/d'),
            $holiday->name
        ];
    }

    /**
     * Format data for specified column
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_YYYYMMDDSLASH,
            'A' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
