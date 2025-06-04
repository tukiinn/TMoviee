<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Episode;
use Illuminate\Support\Str;
use App\Models\Director;
use App\Models\Genre;
use App\Models\Country;
use Illuminate\Support\Facades\Log;
use App\Traits\Searchable;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    use Searchable;

    protected $searchableColumns = [
        'title',
        'sub_title',
        'description',
        'quality',
        'release_year'
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Movie::with(['genres', 'country', 'director', 'actors']);
            
            // Apply search if search parameter exists
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    foreach ($this->searchableColumns as $column) {
                        $q->orWhere($column, 'like', '%' . $search . '%');
                    }
                });
            }

            // Apply sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Get paginated results
            $results = $query->paginate(20);

            return response()->json([
                'data' => $results->items(),
                'total' => $results->total(),
                'per_page' => $results->perPage(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'search' => $request->input('search', ''),
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ]);
        } catch (\Exception $e) {
            Log::error('Movie search error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tìm kiếm phim',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:movies,slug',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|string',
            'banner' => 'nullable|string',
            'trailer_url' => 'nullable|string',
            'video_url' => 'nullable|string',
            'duration' => 'nullable|string',
            'release_year' => 'required|integer',
            'quality' => 'nullable|string',
            'age_rating' => 'nullable|string',
            'imdb_rating' => 'nullable|numeric',
            'is_series' => 'required|boolean',
            'total_episodes' => 'nullable|integer',
            'current_episode' => 'nullable|integer',
            'is_featured' => 'nullable|boolean',
            'is_latest' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'country_id' => 'required|exists:countries,id',
            'director_id' => 'nullable|exists:directors,id',
            'genres' => 'array|nullable',
            'genres.*' => 'exists:genres,id',
        ]);

        $movie = Movie::create($data);

        // Nếu có genres thì sync
        if ($request->has('genres')) {
            $movie->genres()->sync($request->genres);
        }

        return response()->json([
            'message' => 'Tạo phim mới thành công',
            'movie' => $movie->load(['genres', 'country', 'director'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $movie = Movie::with(['genres', 'country', 'director'])->findOrFail($id);
        return response()->json($movie);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        $data = $request->all();
        $movie->update($data);
        // Nếu có genres hoặc country gửi lên thì sync
        if ($request->has('genres')) {
            $movie->genres()->sync($request->genres);
        }
        if ($request->has('country_id')) {
            $movie->country_id = $request->country_id;
            $movie->save();
        }
        return response()->json(['message' => 'Cập nhật thành công', 'movie' => $movie]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();
        return response()->json(['message' => 'Đã xóa phim']);
    }

    public function importFromOphim(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:movies,slug',
                'sub_title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'poster' => 'nullable|string',
                'banner' => 'nullable|string',
                'trailer_url' => 'nullable|string',
                'duration' => 'nullable|integer',
                'release_year' => 'required|integer',
                'quality' => 'nullable|string',
                'is_series' => 'required|boolean',
                'total_episodes' => 'nullable|integer',
                'current_episode' => 'nullable|integer',
                'status' => 'required|boolean',
                'genres' => 'array',
                'country' => 'array',
                'director' => 'array',
                'country_id' => 'nullable|integer|exists:countries,id',
                'actors' => 'array',
                'actors.*.name' => 'nullable|string',
                'actors.*.character_name' => 'nullable|string',
                'imdb_rating' => 'nullable|numeric',
                'tmdb_rating' => 'nullable|numeric',
                'episodes' => 'array',
                'episodes.*.server_name' => 'string|nullable',
                'episodes.*.server_data' => 'array',
                'episodes.*.server_data.*.name' => 'string|nullable',
                'episodes.*.server_data.*.slug' => 'string|nullable',
                'episodes.*.server_data.*.link_embed' => 'string|nullable',
                'episodes.*.server_data.*.link_m3u8' => 'string|nullable',
            ]);

            // Tạo movie nhưng KHÔNG save ngay
            $movie = new Movie();
            $movie->fill($validated);

            // Mapping director
            $director_id = null;
            if (!empty($validated['director']) && is_array($validated['director'])) {
                $directorName = $validated['director'][0] ?? null;
                if ($directorName) {
                    $slug = Str::slug($directorName);
                    $directorModel = Director::where('slug', $slug)->first();
                    if (!$directorModel) {
                        $directorModel = Director::create([
                            'name' => $directorName,
                            'slug' => $slug
                        ]);
                    }
                    $director_id = $directorModel->id;
                }
            }
            $movie->director_id = $director_id;

            // Mapping country
            $country_id = null;
            if (!empty($validated['country']) && is_array($validated['country'])) {
                $countryName = $validated['country'][0]['name'] ?? null;
                $countrySlug = $validated['country'][0]['slug'] ?? ($countryName ? Str::slug($countryName) : null);
                if ($countrySlug) {
                    $countryModel = Country::where('slug', $countrySlug)->first();
                    if (!$countryModel && $countryName) {
                        // Sinh code từ slug: au-my => AM
                        $code = strtoupper(implode('', array_map(function($part) {
                            return mb_substr($part, 0, 1);
                        }, explode('-', $countrySlug))));
                        $countryModel = Country::create([
                            'name' => $countryName,
                            'slug' => $countrySlug,
                            'code' => $code
                        ]);
                    }
                    if ($countryModel) {
                        $country_id = $countryModel->id;
                    }
                }
            }
            $movie->country_id = $country_id;

            // Lưu movie SAU KHI đã gán director_id và country_id
            $movie->save();

            // Ghi log hoạt động thêm phim mới
            Activity::create([
                'user_id' => Auth::id() ?? 1,
                'type' => 'movie',
                'description' => 'Thêm phim mới: ' . $movie->title,
                'subject_id' => $movie->id,
                'subject_type' => Movie::class,
            ]);

            // Xử lý genres
            $genreIds = [];
            if (!empty($validated['genres']) && is_array($validated['genres'])) {
                foreach ($validated['genres'] as $genre) {
                    $genreName = $genre['name'] ?? null;
                    $genreSlug = $genre['slug'] ?? ($genreName ? Str::slug($genreName) : null);
                    if ($genreSlug) {
                        $genreModel = Genre::where('slug', $genreSlug)->first();
                        if (!$genreModel && $genreName) {
                            $genreModel = Genre::create([
                                'name' => $genreName,
                                'slug' => $genreSlug
                            ]);
                        }
                        if ($genreModel) {
                            $genreIds[] = $genreModel->id;
                        }
                    }
                }
                $movie->genres()->sync($genreIds);
            }

            // Gắn diễn viên (tạo mới nếu chưa có)
            if (!empty($validated['actors'])) {
                $syncData = [];
                foreach ($validated['actors'] as $actor) {
                    $name = $actor['name'];
                    $slug = Str::slug($name);
                    if (empty($slug)) {
                        $slug = 'dien-vien-' . uniqid();
                    }
                    $actorModel = \App\Models\Actor::firstOrCreate(
                        ['slug' => $slug],
                        ['name' => $name]
                    );
                    $syncData[$actorModel->id] = ['character_name' => $actor['character_name'] ?? null];
                }
                $movie->actors()->sync($syncData);
            }

            // Nếu là phim bộ, lưu các tập vào bảng episodes
            if ($movie->is_series && !empty($validated['episodes'])) {
                foreach ($validated['episodes'] as $server) {
                    foreach ($server['server_data'] as $ep) {
                        // Tạo tập phim, lưu link_embed vào video_url
                        Episode::create([
                            'movie_id'      => $movie->id,
                            'season'        => 1, // Nếu OPhim có season thì lấy, không thì mặc định 1
                            'episode_number'=> is_numeric($ep['name']) ? intval($ep['name']) : null,
                            'title'         => 'Tập ' . $ep['name'],
                            'duration'      => null, // OPhim không có duration
                            'video_url'     => $ep['link_embed'] ?? null, // Lưu link_embed vào video_url
                            'description'   => null,
                            'thumbnail'     => null,
                            'status'        => 1,
                        ]);
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Import phim từ OPhim thành công',
                'data' => $movie->load('genres', 'actors', 'country')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi import phim: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importFromOphimBulk(Request $request)
    {
        try {
            $request->validate([
                'movies' => 'required|array',
                'movies.*.title' => 'required|string|max:255',
                'movies.*.slug' => 'required|string|max:255|unique:movies,slug',
                'movies.*.sub_title' => 'nullable|string|max:255',
                'movies.*.description' => 'nullable|string',
                'movies.*.poster' => 'nullable|string',
                'movies.*.banner' => 'nullable|string',
                'movies.*.trailer_url' => 'nullable|string',
                'movies.*.duration' => 'nullable|integer',
                'movies.*.release_year' => 'required|integer',
                'movies.*.quality' => 'nullable|string',
                'movies.*.is_series' => 'required|boolean',
                'movies.*.total_episodes' => 'nullable|integer',
                'movies.*.current_episode' => 'nullable|integer',
                'movies.*.status' => 'required|boolean',
                'movies.*.genres' => 'array',
                'movies.*.country' => 'array',
                'movies.*.director' => 'array',
                'movies.*.country_id' => 'nullable|integer|exists:countries,id',
                'movies.*.actors' => 'array',
                'movies.*.actors.*.name' => 'nullable|string',
                'movies.*.actors.*.character_name' => 'nullable|string',
                'movies.*.imdb_rating' => 'nullable|numeric',
                'movies.*.tmdb_rating' => 'nullable|numeric',
                'movies.*.episodes' => 'array',
                'movies.*.episodes.*.server_name' => 'string|nullable',
                'movies.*.episodes.*.server_data' => 'array',
                'movies.*.episodes.*.server_data.*.name' => 'string|nullable',
                'movies.*.episodes.*.server_data.*.slug' => 'string|nullable',
                'movies.*.episodes.*.server_data.*.link_embed' => 'string|nullable',
                'movies.*.episodes.*.server_data.*.link_m3u8' => 'string|nullable',
            ]);

            $importedMovies = [];
            $errors = [];

            foreach ($request->movies as $movieData) {
                try {
                    // Tạo movie nhưng KHÔNG save ngay
                    $movie = new Movie();
                    $movie->fill($movieData);

                    // Mapping director
                    $director_id = null;
                    if (!empty($movieData['director']) && is_array($movieData['director'])) {
                        $directorName = $movieData['director'][0] ?? null;
                        if ($directorName) {
                            $slug = Str::slug($directorName);
                            $directorModel = Director::where('slug', $slug)->first();
                            if (!$directorModel) {
                                $directorModel = Director::create([
                                    'name' => $directorName,
                                    'slug' => $slug
                                ]);
                            }
                            $director_id = $directorModel->id;
                        }
                    }
                    $movie->director_id = $director_id;

                    // Mapping country
                    $country_id = null;
                    if (!empty($movieData['country']) && is_array($movieData['country'])) {
                        $countryName = $movieData['country'][0]['name'] ?? null;
                        $countrySlug = $movieData['country'][0]['slug'] ?? ($countryName ? Str::slug($countryName) : null);
                        if ($countrySlug) {
                            $countryModel = Country::where('slug', $countrySlug)->first();
                            if (!$countryModel && $countryName) {
                                $code = strtoupper(implode('', array_map(function($part) {
                                    return mb_substr($part, 0, 1);
                                }, explode('-', $countrySlug))));
                                $countryModel = Country::create([
                                    'name' => $countryName,
                                    'slug' => $countrySlug,
                                    'code' => $code
                                ]);
                            }
                            if ($countryModel) {
                                $country_id = $countryModel->id;
                            }
                        }
                    }
                    $movie->country_id = $country_id;

                    // Lưu movie
                    $movie->save();

                    // Ghi log hoạt động
                    Activity::create([
                        'user_id' => Auth::id() ?? 1,
                        'type' => 'movie',
                        'description' => 'Thêm phim mới: ' . $movie->title,
                        'subject_id' => $movie->id,
                        'subject_type' => Movie::class,
                    ]);

                    // Xử lý genres
                    $genreIds = [];
                    if (!empty($movieData['genres']) && is_array($movieData['genres'])) {
                        foreach ($movieData['genres'] as $genre) {
                            $genreName = $genre['name'] ?? null;
                            $genreSlug = $genre['slug'] ?? ($genreName ? Str::slug($genreName) : null);
                            if ($genreSlug) {
                                $genreModel = Genre::where('slug', $genreSlug)->first();
                                if (!$genreModel && $genreName) {
                                    $genreModel = Genre::create([
                                        'name' => $genreName,
                                        'slug' => $genreSlug
                                    ]);
                                }
                                if ($genreModel) {
                                    $genreIds[] = $genreModel->id;
                                }
                            }
                        }
                        $movie->genres()->sync($genreIds);
                    }

                    // Gắn diễn viên
                    if (!empty($movieData['actors'])) {
                        $syncData = [];
                        foreach ($movieData['actors'] as $actor) {
                            $name = $actor['name'];
                            $slug = Str::slug($name);
                            if (empty($slug)) {
                                $slug = 'dien-vien-' . uniqid();
                            }
                            $actorModel = \App\Models\Actor::firstOrCreate(
                                ['slug' => $slug],
                                ['name' => $name]
                            );
                            $syncData[$actorModel->id] = ['character_name' => $actor['character_name'] ?? null];
                        }
                        $movie->actors()->sync($syncData);
                    }

                    // Xử lý episodes nếu là phim bộ
                    if ($movie->is_series && !empty($movieData['episodes'])) {
                        foreach ($movieData['episodes'] as $server) {
                            foreach ($server['server_data'] as $ep) {
                                Episode::create([
                                    'movie_id'      => $movie->id,
                                    'season'        => 1,
                                    'episode_number'=> is_numeric($ep['name']) ? intval($ep['name']) : null,
                                    'title'         => 'Tập ' . $ep['name'],
                                    'duration'      => null,
                                    'video_url'     => $ep['link_embed'] ?? null,
                                    'description'   => null,
                                    'thumbnail'     => null,
                                    'status'        => 1,
                                ]);
                            }
                        }
                    }

                    $importedMovies[] = $movie->load('genres', 'actors', 'country');
                } catch (\Exception $e) {
                    $errors[] = [
                        'movie' => $movieData['title'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Import phim thành công',
                'data' => [
                    'imported' => $importedMovies,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi import phim: ' . $e->getMessage()
            ], 500);
        }
    }
}
