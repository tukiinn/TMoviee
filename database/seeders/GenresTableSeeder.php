<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;
use Illuminate\Support\Str;

class GenresTableSeeder extends Seeder
{
    public function run()
    {
        $genres = ['Hành động', 'Tình cảm', 'Hài', 'Kinh dị', 'Phiêu lưu', 'Hoạt hình', 'Tâm lý', 'Viễn tưởng', 'Cổ trang', 'Học đường'];
        foreach ($genres as $name) {
            Genre::create(['name' => $name, 'slug' => Str::slug($name)]);
        }
    }
} 