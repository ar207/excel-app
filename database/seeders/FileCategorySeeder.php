<?php

namespace Database\Seeders;

use App\Models\FileCategory;
use Illuminate\Database\Seeder;

class FileCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr = [
          [
              'name' => 'Active Product Listing',
              'created_at' => now(),
              'updated_at' => now(),
          ], [
              'name' => 'Cardinal Health',
              'created_at' => now(),
              'updated_at' => now(),
          ], [
              'name' => 'Top 500 Product Trending (file)',
              'created_at' => now(),
              'updated_at' => now(),
          ], [
              'name' => 'Auburn Pharmaceutical',
              'created_at' => now(),
              'updated_at' => now(),
          ], [
              'name' => 'Export-all-products (file)',
              'created_at' => now(),
              'updated_at' => now(),
          ],
        ];

        FileCategory::truncate();
        FileCategory::insert($arr);
    }
}
