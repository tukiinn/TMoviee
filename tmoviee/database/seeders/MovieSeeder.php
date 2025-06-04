<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      
        // // Interstellar
        // $interstellarId = DB::table('movies')->insertGetId([
        //     'title' => 'Interstellar',
        //     'slug' => 'interstellar',
        //     'sub_title' => 'Giữa Các Vì Sao',
        //     'description' => 'Một nhóm phi hành gia thực hiện sứ mệnh tìm kiếm hành tinh mới cho nhân loại khi Trái Đất đang dần trở nên không thể sống được.',
        //     'poster' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
        //     'banner' => 'https://image.tmdb.org/t/p/original/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
        //     'trailer_url' => null,
        //     'video_url' => 'https://example.com/interstellar.mp4',
        //     'duration' => 169,
        //     'release_year' => 2014,
        //     'quality' => 'HD',
        //     'age_rating' => 'T13',
        //     'imdb_rating' => 8.6,
        //     'is_series' => false,
        //     'total_episodes' => null,
        //     'current_episode' => null,
        //     'is_featured' => true,
        //     'is_latest' => false,
        //     'status' => true,
        //     'country_id' => DB::table('countries')->where('code', 'US')->value('id'),
        //     'director_id' => DB::table('directors')->where('slug', 'christopher-nolan')->value('id'),
        // ]);
        // DB::table('movie_genre')->insert([
        //     ['movie_id' => $interstellarId, 'genre_id' => DB::table('genres')->where('slug', 'khoa-hoc')->value('id')],
        //     ['movie_id' => $interstellarId, 'genre_id' => DB::table('genres')->where('slug', 'phieu-luu')->value('id')],
        // ]);

        // // Spirited Away
        // $spiritedAwayId = DB::table('movies')->insertGetId([
        //     'title' => 'Spirited Away',
        //     'slug' => 'spirited-away',
        //     'sub_title' => 'Vùng Đất Linh Hồn',
        //     'description' => 'Câu chuyện về cô bé Chihiro bị lạc vào thế giới của các vị thần và phải tìm cách cứu cha mẹ.',
        //     'poster' => 'https://image.tmdb.org/t/p/w500/39wmItIWsg5sZMyRUHLkWBcuVCM.jpg',
        //     'banner' => 'https://image.tmdb.org/t/p/original/39wmItIWsg5sZMyRUHLkWBcuVCM.jpg',
        //     'trailer_url' => null,
        //     'video_url' => 'https://example.com/spirited-away.mp4',
        //     'duration' => 125,
        //     'release_year' => 2001,
        //     'quality' => 'HD',
        //     'age_rating' => 'T13',
        //     'imdb_rating' => 8.6,
        //     'is_series' => false,
        //     'total_episodes' => null,
        //     'current_episode' => null,
        //     'is_featured' => false,
        //     'is_latest' => true,
        //     'status' => true,
        //     'country_id' => DB::table('countries')->where('code', 'JP')->value('id'),
        //     'director_id' => null,
        // ]);
        // DB::table('movie_genre')->insert([
        //     ['movie_id' => $spiritedAwayId, 'genre_id' => DB::table('genres')->where('slug', 'hoat-hinh')->value('id')],
        //     ['movie_id' => $spiritedAwayId, 'genre_id' => DB::table('genres')->where('slug', 'phieu-luu')->value('id')],
        // ]);

        // // The Shawshank Redemption
        // $shawshankId = DB::table('movies')->insertGetId([
        //     'title' => 'The Shawshank Redemption',
        //     'slug' => 'the-shawshank-redemption',
        //     'sub_title' => 'Niềm Hy Vọng',
        //     'description' => 'Câu chuyện về tình bạn giữa hai tù nhân trong nhà tù Shawshank và hành trình tìm kiếm tự do của họ.',
        //     'poster' => 'https://image.tmdb.org/t/p/w500/q6y0Go1tsGEsmtFryDOJo3dEmqu.jpg',
        //     'banner' => 'https://image.tmdb.org/t/p/original/q6y0Go1tsGEsmtFryDOJo3dEmqu.jpg',
        //     'trailer_url' => null,
        //     'video_url' => 'https://example.com/shawshank.mp4',
        //     'duration' => 142,
        //     'release_year' => 1994,
        //     'quality' => 'HD',
        //     'age_rating' => 'T16',
        //     'imdb_rating' => 9.3,
        //     'is_series' => false,
        //     'total_episodes' => null,
        //     'current_episode' => null,
        //     'is_featured' => true,
        //     'is_latest' => false,
        //     'status' => true,
        //     'country_id' => DB::table('countries')->where('code', 'US')->value('id'),
        //     'director_id' => null,
        // ]);
        // DB::table('movie_genre')->insert([
        //     ['movie_id' => $shawshankId, 'genre_id' => DB::table('genres')->where('slug', 'chinh-kich')->value('id')],
        //     ['movie_id' => $shawshankId, 'genre_id' => DB::table('genres')->where('slug', 'hinh-su')->value('id')],
        // ]);

        // // Oldboy
        // $oldboyId = DB::table('movies')->insertGetId([
        //     'title' => 'Oldboy',
        //     'slug' => 'oldboy',
        //     'sub_title' => 'Cựu Binh',
        //     'description' => 'Một người đàn ông bị giam cầm trong 15 năm mà không biết lý do, khi được thả ra, anh ta bắt đầu hành trình tìm kiếm sự thật.',
        //     'poster' => 'https://image.tmdb.org/t/p/w500/pWDtjs568ZfOTMbURQBYuT4Qxka.jpg',
        //     'banner' => 'https://image.tmdb.org/t/p/original/pWDtjs568ZfOTMbURQBYuT4Qxka.jpg',
        //     'trailer_url' => null,
        //     'video_url' => 'https://example.com/oldboy.mp4',
        //     'duration' => 120,
        //     'release_year' => 2003,
        //     'quality' => 'HD',
        //     'age_rating' => 'T18',
        //     'imdb_rating' => 8.4,
        //     'is_series' => false,
        //     'total_episodes' => null,
        //     'current_episode' => null,
        //     'is_featured' => false,
        //     'is_latest' => true,
        //     'status' => true,
        //     'country_id' => DB::table('countries')->where('code', 'KR')->value('id'),
        //     'director_id' => null,
        // ]);
        // DB::table('movie_genre')->insert([
        //     ['movie_id' => $oldboyId, 'genre_id' => DB::table('genres')->where('slug', 'hanh-dong')->value('id')],
        //     ['movie_id' => $oldboyId, 'genre_id' => DB::table('genres')->where('slug', 'hinh-su')->value('id')],
        // ]);

        // // The Grand Budapest Hotel
        // $budapestId = DB::table('movies')->insertGetId([
        //     'title' => 'The Grand Budapest Hotel',
        //     'slug' => 'the-grand-budapest-hotel',
        //     'sub_title' => 'Khách Sạn Budapest',
        //     'description' => 'Câu chuyện về những cuộc phiêu lưu của một quản lý khách sạn và cậu học việc trong thời kỳ giữa hai cuộc chiến tranh thế giới.',
        //     'poster' => 'https://image.tmdb.org/t/p/w500/eWdyYQreja6JGCzqHWXpWHDrrOc.jpg',
        //     'banner' => 'https://image.tmdb.org/t/p/original/eWdyYQreja6JGCzqHWXpWHDrrOc.jpg',
        //     'trailer_url' => null,
        //     'video_url' => 'https://example.com/budapest.mp4',
        //     'duration' => 99,
        //     'release_year' => 2014,
        //     'quality' => 'HD',
        //     'age_rating' => 'T13',
        //     'imdb_rating' => 8.1,
        //     'is_series' => false,
        //     'total_episodes' => null,
        //     'current_episode' => null,
        //     'is_featured' => false,
        //     'is_latest' => false,
        //     'status' => true,
        //     'country_id' => DB::table('countries')->where('code', 'US')->value('id'),
        //     'director_id' => null,
        // ]);
        // DB::table('movie_genre')->insert([
        //     ['movie_id' => $budapestId, 'genre_id' => DB::table('genres')->where('slug', 'hai')->value('id')],
        //     ['movie_id' => $budapestId, 'genre_id' => DB::table('genres')->where('slug', 'phieu-luu')->value('id')],
        // ]);

        // Breaking Bad
    //     $breakingBadId = DB::table('movies')->insertGetId([
    //         'title' => 'Breaking Bad',
    //         'slug' => 'breaking-bad',
    //         'sub_title' => 'Biệt Đội Siêu Đẳng',
    //         'description' => 'Câu chuyện về một giáo viên hóa học bị ung thư phổi, bắt đầu sản xuất và buôn bán ma túy để đảm bảo tương lai tài chính cho gia đình.',
    //         'poster' => 'https://image.tmdb.org/t/p/w500/3x7j600L5vRzWgvVJg8QwHd3T9q.jpg',
    //         'banner' => 'https://image.tmdb.org/t/p/original/3x7j600L5vRzWgvVJg8QwHd3T9q.jpg',
    //         'trailer_url' => null,
    //         'video_url' => 'https://example.com/breaking-bad.mp4',
    //         'duration' => 45, // Thời lượng trung bình mỗi tập
    //         'release_year' => 2008,
    //         'quality' => 'HD',
    //         'age_rating' => 'T18',
    //         'imdb_rating' => 9.5,
    //         'is_series' => true, // Đánh dấu là phim bộ
    //         'total_episodes' => 62, // Tổng số tập
    //         'current_episode' => 62, // Tập mới nhất
    //         'is_featured' => true,
    //         'is_latest' => false,
    //         'status' => true,
    //         'country_id' => DB::table('countries')->where('code', 'US')->value('id'),
    //         'director_id' => null,
    //     ]);
    //     DB::table('movie_genre')->insert([
    //         ['movie_id' => $breakingBadId, 'genre_id' => DB::table('genres')->where('slug', 'chinh-kich')->value('id')],
    //         ['movie_id' => $breakingBadId, 'genre_id' => DB::table('genres')->where('slug', 'hinh-su')->value('id')],
    //     ]);

    //     // Thêm các tập phim
    //     $seasons = [
    //         [
    //             'season' => 1,
    //             'episodes' => [
    //                 ['title' => 'Pilot', 'episode_number' => 1, 'duration' => 58, 'video_url' => 'https://example.com/bb-s1e1.mp4'],
    //                 ['title' => 'Cat\'s in the Bag...', 'episode_number' => 2, 'duration' => 48, 'video_url' => 'https://example.com/bb-s1e2.mp4'],
    //                 // Thêm các tập khác...
    //             ]
    //         ],
    //         [
    //             'season' => 2,
    //             'episodes' => [
    //                 ['title' => 'Seven Thirty-Seven', 'episode_number' => 1, 'duration' => 47, 'video_url' => 'https://example.com/bb-s2e1.mp4'],
    //                 ['title' => 'Grilled', 'episode_number' => 2, 'duration' => 45, 'video_url' => 'https://example.com/bb-s2e2.mp4'],
    //                 // Thêm các tập khác...
    //             ]
    //         ],
    //         // Thêm các mùa khác...
    //     ];

    //     foreach ($seasons as $season) {
    //         foreach ($season['episodes'] as $episode) {
    //             DB::table('episodes')->insert([
    //                 'movie_id' => $breakingBadId,
    //                 'season' => $season['season'],
    //                 'episode_number' => $episode['episode_number'],
    //                 'title' => $episode['title'],
    //                 'duration' => $episode['duration'],
    //                 'video_url' => $episode['video_url'],
    //                 'status' => true,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //         }
    //     }

    //     // Thêm diễn viên
    //     DB::table('movie_actor')->insert([
    //         ['movie_id' => $breakingBadId, 'actor_id' => DB::table('actors')->where('slug', 'bryan-cranston')->value('id'), 'character_name' => 'Walter White'],
    //         ['movie_id' => $breakingBadId, 'actor_id' => DB::table('actors')->where('slug', 'aaron-paul')->value('id'), 'character_name' => 'Jesse Pinkman'],
    //     ]);
    }
}
