<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Actor;
use Illuminate\Support\Str;

class MoviesTableSeeder extends Seeder
{
    public function run()
    {
        $genreIds = Genre::pluck('id')->toArray();
        $actorIds = Actor::pluck('id')->toArray();
        $sampleVideos = [
            '/videos/video1.mp4',
            '/videos/video2.mp4',
            '/videos/video3.mp4',
            '/videos/video4.mp4',
            '/videos/video5.mp4',
        ];
        $sampleTrailers = [
            'https://www.youtube.com/watch?v=abc123',
            'https://www.youtube.com/watch?v=def456',
            'https://www.youtube.com/watch?v=ghi789',
            'https://www.youtube.com/watch?v=jkl012',
            'https://www.youtube.com/watch?v=mno345',
        ];

        for ($i = 1; $i <= 10; $i++) {
            $movie = Movie::create([
                'title' => "Phim Bộ $i",
                'slug' => Str::slug("Phim Bộ $i"),
                'sub_title' => "Sub Title $i",
                'description' => "Mô tả phim bộ $i",
                'poster' => "/images/poster$i.jpg",
                'banner' => "/images/banner$i.jpg",
                'trailer_url' => $sampleTrailers[array_rand($sampleTrailers)],
                'video_url' => $sampleVideos[array_rand($sampleVideos)],
                'duration' => 45,
                'release_year' => 2023,
                'quality' => 'HD',
                'age_rating' => 'T13',
                'imdb_rating' => 8 + ($i / 10),
                'is_series' => 1,
                'total_episodes' => 20,
                'current_episode' => 10 + $i,
                'is_featured' => 1,
                'is_latest' => 1,
                'status' => 1,
                'country_id' => 1,
                'director_id' => 1,
            ]);
            // Gán ngẫu nhiên 2-3 genres cho mỗi phim
            $movie->genres()->attach(array_rand(array_flip($genreIds), rand(2, 3)));
            // Gán ngẫu nhiên 3-5 actors cho mỗi phim
            $movie->actors()->attach(array_rand(array_flip($actorIds), rand(3, 5)));
        }
    }
} 