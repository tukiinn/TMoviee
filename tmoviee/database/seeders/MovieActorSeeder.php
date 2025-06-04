<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovieActorSeeder extends Seeder
{
    public function run(): void
    {
        // Thêm diễn viên cho Interstellar
        DB::table('movie_actor')->insert([
            ['movie_id' => DB::table('movies')->where('slug', 'interstellar')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'matthew-mcconaughey')->value('id'), 'character_name' => 'Cooper'],
            ['movie_id' => DB::table('movies')->where('slug', 'interstellar')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'anne-hathaway')->value('id'), 'character_name' => 'Brand'],
            ['movie_id' => DB::table('movies')->where('slug', 'interstellar')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'jessica-chastain')->value('id'), 'character_name' => 'Murphy'],
        ]);

        // Thêm diễn viên cho Spirited Away
        DB::table('movie_actor')->insert([
            ['movie_id' => DB::table('movies')->where('slug', 'spirited-away')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'rumi-hiiragi')->value('id'), 'character_name' => 'Chihiro'],
            ['movie_id' => DB::table('movies')->where('slug', 'spirited-away')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'miyu-irino')->value('id'), 'character_name' => 'Haku'],
        ]);

        // Thêm diễn viên cho The Shawshank Redemption
        DB::table('movie_actor')->insert([
            ['movie_id' => DB::table('movies')->where('slug', 'the-shawshank-redemption')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'tim-robbins')->value('id'), 'character_name' => 'Andy Dufresne'],
            ['movie_id' => DB::table('movies')->where('slug', 'the-shawshank-redemption')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'morgan-freeman')->value('id'), 'character_name' => 'Ellis Boyd "Red" Redding'],
        ]);

        // Thêm diễn viên cho Oldboy
        DB::table('movie_actor')->insert([
            ['movie_id' => DB::table('movies')->where('slug', 'oldboy')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'choi-min-sik')->value('id'), 'character_name' => 'Oh Dae-su'],
            ['movie_id' => DB::table('movies')->where('slug', 'oldboy')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'yoo-ji-tae')->value('id'), 'character_name' => 'Lee Woo-jin'],
        ]);

        // Thêm diễn viên cho The Grand Budapest Hotel
        DB::table('movie_actor')->insert([
            ['movie_id' => DB::table('movies')->where('slug', 'the-grand-budapest-hotel')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'ralph-fiennes')->value('id'), 'character_name' => 'M. Gustave'],
            ['movie_id' => DB::table('movies')->where('slug', 'the-grand-budapest-hotel')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'tony-revolori')->value('id'), 'character_name' => 'Zero Moustafa'],
        ]);

        // Thêm diễn viên cho Breaking Bad
        DB::table('movie_actor')->insert([
            ['movie_id' => DB::table('movies')->where('slug', 'breaking-bad')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'bryan-cranston')->value('id'), 'character_name' => 'Walter White'],
            ['movie_id' => DB::table('movies')->where('slug', 'breaking-bad')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'aaron-paul')->value('id'), 'character_name' => 'Jesse Pinkman'],
            ['movie_id' => DB::table('movies')->where('slug', 'breaking-bad')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'anna-gunn')->value('id'), 'character_name' => 'Skyler White'],
            ['movie_id' => DB::table('movies')->where('slug', 'breaking-bad')->value('id'), 'actor_id' => DB::table('actors')->where('slug', 'dean-norris')->value('id'), 'character_name' => 'Hank Schrader'],
        ]);
    }
} 