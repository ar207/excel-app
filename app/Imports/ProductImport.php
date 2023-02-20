<?php

namespace App\Imports;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductImport implements ToCollection, WithChunkReading
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
                $arr[$count]['product_id'] = isset($row[0]) ? $row[0] : '';
                $arr[$count]['dosage_form'] = isset($row[6]) ? $row[6] : '';
                $arr[$count]['labeler_name'] = isset($row[12]) ? $row[12] : '';
                $arr[$count]['name'] = isset($row[13]) ? $row[13] : '';
                $arr[$count]['strength'] = isset($row[14]) ? $row[14] : '';
                $arr[$count]['created_at'] = Carbon::now();
                $arr[$count]['updated_at'] = Carbon::now();
                $count++;
            }
        }

        if (!empty($arr)) {
            unset($arr[0]);
        }
        foreach (array_chunk($arr, 1000) as $ind) {
            Product::insert($ind);
        }
    }

    public function chunkSize(): int
    {
        return 5000;
    }
}
