<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class CompanyExport implements FromCollection, WithHeadings, WithMapping
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
            trans('app.company_name'),
            trans('app.prefectures'),
            trans('app.municipalities'),
            trans('app.tel'),
        ];
    }

    /**
     * CSV content
     */
    public function map($company): array
    {
        return [
            $company['name'],
            $company['prefecture'],
            $company['city'],
            $company['tel']
        ];
    }
}
