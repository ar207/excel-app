<?php

namespace App\Imports;

use App\Models\ActiveProductListing;
use App\Models\FDA;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ActiveImport implements ToCollection, WithChunkReading
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $count = 0;
        $arr = [];
        foreach ($collection->toArray() as $row)
        {
            if (!empty($row[0])) {
                if (!empty($row[6])) {
                    $ndc = str_replace('-', '', $row[6]);
                    $fdaData = FDA::where('ndc_match', $ndc)->first();
                    if (!empty($fdaData)) {
                        $fdaData = $fdaData->toArray();
                    }
                    $arr[$count]['desc_one'] = isset($row[1]) ? $row[1] : '';
                    $arr[$count]['vendor'] = isset($row[5]) ? $row[5] : '';
                    $arr[$count]['ndc'] = isset($row[6]) ? $row[6] : '';
                    $arr[$count]['list_price'] = isset($row[4]) ? $row[4] : '';
                    $arr[$count]['name'] = !empty($fdaData['name']) ? $fdaData['name'] : '';
                    $arr[$count]['strength'] = !empty($fdaData['strength']) ? $fdaData['strength'] : '';
                    $arr[$count]['form'] = !empty($fdaData['form']) ? $fdaData['form'] : '';
                    $arr[$count]['count'] = !empty($fdaData['count']) ? $fdaData['count'] : '';
                    $arr[$count]['fda_ndc'] = !empty($fdaData['ndc']) ? $fdaData['ndc'] : '';
                    $arr[$count]['gpw'] = 'Gpw';
                    $arr[$count]['created_at'] = now();
                    $arr[$count]['updated_at'] = now();
                }
                $count++;
            }
        }

        if (!empty($arr)) {
            unset($arr[0], $arr[1]);
        }
        if (!empty($arr)) {
            foreach (array_chunk($arr, 1000) as $ind) {
                ActiveProductListing::insert($ind);
            }
        }
    }

    public function chunkSize(): int
    {
        return 5000;
    }
}
