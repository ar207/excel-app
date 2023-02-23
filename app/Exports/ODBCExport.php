<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ODBCExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $data;

    /**
     * Write code on Method
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Write code on Method
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->data);
    }

    /**
     * Write code on Method
     *
     * @return array ()
     */
    public function headings(): array
    {
        $heading = [
            'Product No',
            'Ndc',
            'Name',
            'Strength',
            'Form',
            'Count',
            'Gpw',
            'Cardinal',
            'Exirx',
            'Txrade',
            'Auburn',
        ];

        return $heading;
    }
}
