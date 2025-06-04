<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('genres')->insert([
            ['name' => 'Hành Động', 'slug' => 'hanh-dong', 'description' => 'Phim hành động', 'status' => 1],
            ['name' => 'Phiêu Lưu', 'slug' => 'phieu-luu', 'description' => 'Phim phiêu lưu', 'status' => 1],
            ['name' => 'Khoa Học', 'slug' => 'khoa-hoc', 'description' => 'Phim khoa học', 'status' => 1],
            ['name' => 'Tình Cảm', 'slug' => 'tinh-cam', 'description' => 'Phim tình cảm', 'status' => 1],
            ['name' => 'Hài', 'slug' => 'hai', 'description' => 'Phim hài', 'status' => 1],
            ['name' => 'Kinh Dị', 'slug' => 'kinh-di', 'description' => 'Phim kinh dị', 'status' => 1],
            ['name' => 'Hoạt Hình', 'slug' => 'hoat-hinh', 'description' => 'Phim hoạt hình', 'status' => 1],
            ['name' => 'Chính Kịch', 'slug' => 'chinh-kich', 'description' => 'Phim chính kịch', 'status' => 1],
            ['name' => 'Hình Sự', 'slug' => 'hinh-su', 'description' => 'Phim hình sự', 'status' => 1],
        ]);
    }
}
