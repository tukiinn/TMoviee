<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('countries')->insert([
            ['name' => 'Việt Nam', 'slug' => 'viet-nam', 'code' => 'VN', 'status' => 1],
            ['name' => 'Hoa Kỳ', 'slug' => 'hoa-ky', 'code' => 'US', 'status' => 1],
            ['name' => 'Hàn Quốc', 'slug' => 'han-quoc', 'code' => 'KR', 'status' => 1],
            ['name' => 'Nhật Bản', 'slug' => 'nhat-ban', 'code' => 'JP', 'status' => 1],
            ['name' => 'Trung Quốc', 'slug' => 'trung-quoc', 'code' => 'CN', 'status' => 1],
            ['name' => 'Anh', 'slug' => 'anh', 'code' => 'GB', 'status' => 1],
            ['name' => 'Pháp', 'slug' => 'phap', 'code' => 'FR', 'status' => 1],
        ]);
    }
}
