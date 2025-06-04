<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Actor;
use Illuminate\Support\Str;

class ActorsTableSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 20; $i++) {
            Actor::create([
                'name' => "Diễn viên $i",
                'slug' => Str::slug("Diễn viên $i"),
                'avatar' => "/images/actor$i.jpg"
            ]);
        }
    }
} 