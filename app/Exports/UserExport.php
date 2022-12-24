<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Data to export
     */
    protected $data;

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
        return (collect($this->data));
    }

    /**
     * CSV Header
     */
    public function headings(): array
    {
        return [
            trans('app.belong'),
            trans('app.name'),
            trans('app.company_name'),
            trans('app.prefectures'),
            trans('app.municipalities'),
        ];
    }

    /**
     * CSV content
     */
    public function map($data): array
    {
        return [
            $data['departments'],
            $data['name'],
            $data['company'],
            $data['prefecture'],
            $data['city']
        ];
    }
}
