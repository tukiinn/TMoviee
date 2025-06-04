<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DirectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('directors')->insert([
            ['name' => 'Christopher Nolan', 'slug' => 'christopher-nolan', 'biography' => 'Đạo diễn nổi tiếng người Anh.', 'avatar' => null, 'birth_date' => '1970-07-30', 'birth_place' => 'London, Anh', 'status' => 1],
            ['name' => 'Bong Joon-ho', 'slug' => 'bong-joon-ho', 'biography' => 'Đạo diễn nổi tiếng người Hàn Quốc.', 'avatar' => null, 'birth_date' => '1969-09-14', 'birth_place' => 'Daegu, Hàn Quốc', 'status' => 1],
            ['name' => 'Anthony Russo', 'slug' => 'anthony-russo', 'biography' => 'Đạo diễn người Mỹ.', 'avatar' => null, 'birth_date' => '1970-02-03', 'birth_place' => 'Cleveland, Ohio, Mỹ', 'status' => 1],
            ['name' => 'Joe Russo', 'slug' => 'joe-russo', 'biography' => 'Đạo diễn người Mỹ.', 'avatar' => null, 'birth_date' => '1971-07-18', 'birth_place' => 'Cleveland, Ohio, Mỹ', 'status' => 1],
        ]);
    }
}
