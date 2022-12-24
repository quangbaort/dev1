<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrganizationExport implements FromCollection, WithHeadings, WithMapping
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
            trans('app.name'),
            trans('app.prefectures'),
            trans('app.city'),
            trans('app.tel')
        ];
    }

    /**
     * CSV content
     */
    public function map($data): array
    {
        return [
            $data['name'],
            $data['prefecture'],
            $data['city'],
            $data['tel']
        ];
    }
}
