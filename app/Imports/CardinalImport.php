<?php

namespace App\Imports;

use App\Models\CardinalHealth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CardinalImport implements ToCollection, WithChunkReading
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $count = 1;
        $arrProductListing = [];
        foreach ($collection->toArray() as $row)
        {
            if (!empty($row[1])) {
                $arrProductListing[$count]['cin_ndc_upc'] = isset($row[0]) ? $row[0] : '';
                $arrProductListing[$count]['cin_ndc_upc1'] = isset($row[1]) ? $row[1] : '';
                $arrProductListing[$count]['trade_name_mfr'] = isset($row[2]) ? $row[2] : '';
                $arrProductListing[$count]['trade_name_mfr2'] = isset($row[3]) ? $row[3] : '';
                $arrProductListing[$count]['strength'] = isset($row[4]) ? $row[4] : '';
                $arrProductListing[$count]['from'] = isset($row[5]) ? $row[5] : '';
                $arrProductListing[$count]['size'] = isset($row[6]) ? $row[6] : '';
                $arrProductListing[$count]['type'] = isset($row[7]) ? $row[7] : '';
                $arrProductListing[$count]['net_cost'] = isset($row[8]) ? $row[8] : '';
                $arrProductListing[$count]['invoice_cost'] = isset($row[9]) ? $row[9] : '';
                $arrProductListing[$count]['cardinal'] = 'Cardinal';
                $count++;
            }
        }

        if (!empty($arrProductListing)) {
            unset($arrProductListing[0]);
        }

        foreach (array_chunk($arrProductListing, 500) as $ind) {
            CardinalHealth::insert($ind);
        }
    }

    public function chunkSize(): int
    {
        return 5000;
    }
}
