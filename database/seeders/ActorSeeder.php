<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('actors')->insert([
            // Interstellar
            ['name' => 'Matthew McConaughey', 'slug' => 'matthew-mcconaughey', 'biography' => 'Nam diễn viên người Mỹ, nổi tiếng với vai Cooper trong Interstellar.', 'avatar' => null, 'birth_date' => '1969-11-04', 'birth_place' => 'Uvalde, Texas, Mỹ', 'status' => 1],
            ['name' => 'Anne Hathaway', 'slug' => 'anne-hathaway', 'biography' => 'Nữ diễn viên người Mỹ, nổi tiếng với vai Brand trong Interstellar.', 'avatar' => null, 'birth_date' => '1982-11-12', 'birth_place' => 'Brooklyn, New York, Mỹ', 'status' => 1],
            ['name' => 'Jessica Chastain', 'slug' => 'jessica-chastain', 'biography' => 'Nữ diễn viên người Mỹ, nổi tiếng với vai Murphy trong Interstellar.', 'avatar' => null, 'birth_date' => '1977-03-24', 'birth_place' => 'Sacramento, California, Mỹ', 'status' => 1],

            // Spirited Away
            ['name' => 'Rumi Hiiragi', 'slug' => 'rumi-hiiragi', 'biography' => 'Nữ diễn viên lồng tiếng người Nhật, nổi tiếng với vai Chihiro trong Spirited Away.', 'avatar' => null, 'birth_date' => '1987-08-01', 'birth_place' => 'Tokyo, Nhật Bản', 'status' => 1],
            ['name' => 'Miyu Irino', 'slug' => 'miyu-irino', 'biography' => 'Nam diễn viên lồng tiếng người Nhật, nổi tiếng với vai Haku trong Spirited Away.', 'avatar' => null, 'birth_date' => '1988-02-19', 'birth_place' => 'Tokyo, Nhật Bản', 'status' => 1],

            // The Shawshank Redemption
            ['name' => 'Tim Robbins', 'slug' => 'tim-robbins', 'biography' => 'Nam diễn viên người Mỹ, nổi tiếng với vai Andy Dufresne trong The Shawshank Redemption.', 'avatar' => null, 'birth_date' => '1958-10-16', 'birth_place' => 'West Covina, California, Mỹ', 'status' => 1],
            ['name' => 'Morgan Freeman', 'slug' => 'morgan-freeman', 'biography' => 'Nam diễn viên người Mỹ, nổi tiếng với vai Ellis Boyd "Red" Redding trong The Shawshank Redemption.', 'avatar' => null, 'birth_date' => '1937-06-01', 'birth_place' => 'Memphis, Tennessee, Mỹ', 'status' => 1],

            // Oldboy
            ['name' => 'Choi Min-sik', 'slug' => 'choi-min-sik', 'biography' => 'Nam diễn viên người Hàn Quốc, nổi tiếng với vai Oh Dae-su trong Oldboy.', 'avatar' => null, 'birth_date' => '1962-01-22', 'birth_place' => 'Seoul, Hàn Quốc', 'status' => 1],
            ['name' => 'Yoo Ji-tae', 'slug' => 'yoo-ji-tae', 'biography' => 'Nam diễn viên người Hàn Quốc, nổi tiếng với vai Lee Woo-jin trong Oldboy.', 'avatar' => null, 'birth_date' => '1976-04-13', 'birth_place' => 'Seoul, Hàn Quốc', 'status' => 1],

            // The Grand Budapest Hotel
            ['name' => 'Ralph Fiennes', 'slug' => 'ralph-fiennes', 'biography' => 'Nam diễn viên người Anh, nổi tiếng với vai M. Gustave trong The Grand Budapest Hotel.', 'avatar' => null, 'birth_date' => '1962-12-22', 'birth_place' => 'Ipswich, Anh', 'status' => 1],
            ['name' => 'Tony Revolori', 'slug' => 'tony-revolori', 'biography' => 'Nam diễn viên người Mỹ, nổi tiếng với vai Zero Moustafa trong The Grand Budapest Hotel.', 'avatar' => null, 'birth_date' => '1996-04-28', 'birth_place' => 'Anaheim, California, Mỹ', 'status' => 1],

            // Breaking Bad
            ['name' => 'Anna Gunn', 'slug' => 'anna-gunn', 'biography' => 'Nữ diễn viên người Mỹ, nổi tiếng với vai Skyler White trong Breaking Bad.', 'avatar' => null, 'birth_date' => '1968-08-11', 'birth_place' => 'Santa Fe, New Mexico, Mỹ', 'status' => 1],
            ['name' => 'Dean Norris', 'slug' => 'dean-norris', 'biography' => 'Nam diễn viên người Mỹ, nổi tiếng với vai Hank Schrader trong Breaking Bad.', 'avatar' => null, 'birth_date' => '1963-04-08', 'birth_place' => 'South Bend, Indiana, Mỹ', 'status' => 1],
        ]);
    }
}
