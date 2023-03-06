<?php

namespace App\Imports;

use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PackageImport implements ToCollection, WithChunkReading
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
                $description = '';
                if (!empty($row[3])) {
                    $description = explode('in', $row[3]);
                }
                $arr[$count]['product_id'] = isset($row[0]) ? $row[0] : '';
                $arr[$count]['ndc_code'] = isset($row[2]) ? ndcCorrection($row[2]) : '';
                $arr[$count]['description'] = isset($description[0]) ? $description[0] : '';
                $arr[$count]['created_at'] = Carbon::now();
                $arr[$count]['updated_at'] = Carbon::now();
                $count++;
            }
        }

        if (!empty($arr)) {
            unset($arr[0]);
        }
        foreach (array_chunk($arr, 1000) as $ind) {
            Package::insert($ind);
        }
    }

    public function chunkSize(): int
    {
        return 5000;
    }
}
