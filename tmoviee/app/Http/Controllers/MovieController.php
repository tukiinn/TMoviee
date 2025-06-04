<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{
    public function getLatest()
    {
        $movies = Movie::where('is_latest', true)
            ->where('status', true)
            ->with(['genres', 'country', 'director'])
            ->take(12)
            ->get();

        return response()->json($movies);
    }

    public function getFeatured()
    {
        $movies = Movie::where('is_featured', true)
            ->where('status', true)
            ->with(['genres', 'country', 'director'])
            ->take(12)
            ->get();

        return response()->json($movies);
    }

    public function latest()
    {
        return Movie::with(['genres', 'country'])
            ->latest()
            ->take(10)
            ->get();
    }

    public function byGenre($genreId)
    {
        return Movie::with(['genres', 'country'])
            ->whereHas('genres', function($query) use ($genreId) {
                $query->where('genres.id', $genreId);
            })
            ->latest()
            ->take(10)
            ->get();
    }

    public function byGenreSlug($slug)
    {
        return Movie::with(['genres', 'country'])
            ->whereHas('genres', function($query) use ($slug) {
                $query->where('genres.slug', $slug);
            })
            ->latest()
            ->paginate(24);
    }

    public function byCountrySlug($slug)
    {
        try {
            $country = Country::where('slug', $slug)
                ->where('status', true)
                ->firstOrFail();

            $movies = Movie::with(['genres', 'actors', 'country'])
                ->where('country_id', $country->id)
                ->where('status', true)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json($movies);
        } catch (\Exception $e) {
            Log::error('Error fetching movies by country:', [
                'slug' => $slug,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Không tìm thấy phim cho quốc gia này'
            ], 404);
        }
    }

    public function show($slug)
    {
        $movie = Movie::where('slug', $slug)
            ->with(['genres', 'country', 'actors', 'director'])
            ->firstOrFail();

        // Log::info('Movie data:', ['movie' => $movie->toArray()]);
        return response()->json($movie);
    }

    public function episodes($slug)
    {
        $movie = Movie::where('slug', $slug)
            ->where('is_series', true)
            ->firstOrFail();

        $episodes = DB::table('episodes')
            ->where('movie_id', $movie->id)
            ->where('status', true)
            ->orderBy('season')
            ->orderBy('episode_number')
            ->get();

        return response()->json($episodes);
    }

    public function related($slug)
    {
        $movie = Movie::where('slug', $slug)->firstOrFail();

        // Lấy phim cùng thể loại
        return Movie::with(['genres', 'country'])
            ->whereHas('genres', function($query) use ($movie) {
                $query->whereIn('genres.id', $movie->genres->pluck('id'));
            })
            ->where('id', '!=', $movie->id)
            ->take(6)
            ->get();
    }

    public function filter(Request $request)
    {
        try {
            // Check if Movie model exists
            if (!class_exists(Movie::class)) {
                throw new \Exception('Movie model not found');
            }

            $query = Movie::query()->where('status', true)
                ->with(['genres', 'country', 'director']);

            // Filter by genres
            if ($request->has('genres') && !empty($request->genres)) {
                $query->whereHas('genres', function($q) use ($request) {
                    $q->whereIn('genres.id', $request->genres);
                });
            }

            // Filter by countries
            if ($request->has('countries') && !empty($request->countries)) {
                $query->whereIn('country_id', $request->countries);
            }

            // Filter by years
            if ($request->has('years') && !empty($request->years)) {
                $query->whereIn('release_year', $request->years);
            }

            // Filter by qualities
            if ($request->has('qualities') && !empty($request->qualities)) {
                $query->whereIn('quality', $request->qualities);
            }

            // Sort by
            switch ($request->sort) {
                case 'rating':
                    $query->orderBy('imdb_rating', 'desc');
                    break;
                case 'views':
                    $query->orderBy('views', 'desc');
                    break;
                default:
                    $query->latest();
            }

            // Check if query is valid
            try {
                $testQuery = clone $query;
                $testQuery->limit(1)->get();
            } catch (\Exception $e) {
                throw $e;
            }

            $perPage = $request->get('per_page', 20);
            $movies = $query->paginate($perPage);

            return response()->json([
                'data' => $movies->items(),
                'total' => $movies->total(),
                'current_page' => $movies->currentPage(),
                'per_page' => $movies->perPage(),
                'last_page' => $movies->lastPage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'total' => 0,
                'current_page' => 1,
                'per_page' => $request->get('per_page', 20),
                'last_page' => 1,
                'error' => 'Có lỗi xảy ra khi tìm kiếm phim'
            ], 500);
        }
    }

    public function getSingleMovies(Request $request)
    {
        $query = Movie::where('is_series', false)
            ->where('status', true)
            ->with(['genres', 'country', 'director']);

        // Filter by genres
        if ($request->has('genres')) {
            $query->whereHas('genres', function($q) use ($request) {
                $q->whereIn('genres.id', $request->genres);
            });
        }

        // Filter by countries
        if ($request->has('countries')) {
            $query->whereIn('country_id', $request->countries);
        }

        // Filter by years
        if ($request->has('years')) {
            $query->whereIn('release_year', $request->years);
        }

        // Filter by qualities
        if ($request->has('qualities')) {
            $query->whereIn('quality', $request->qualities);
        }

        // Sort
        switch ($request->sort) {
            case 'rating':
                $query->orderBy('imdb_rating', 'desc');
                break;
            case 'views':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $movies = $query->paginate(24);

        // Update pagination URLs
        $movies->withPath('/single');
        $movies->appends($request->except('page'));

        // Log the query and results
        // Log::info('Single Movies Query:', [
        //     'params' => $request->all(),
        //     'sql' => $query->toSql(),
        //     'bindings' => $query->getBindings(),
        //     'count' => $movies->total()
        // ]);

        return response()->json($movies);
    }

    public function getSeriesMovies(Request $request)
    {
        $query = Movie::where('is_series', true)
            ->where('status', true)
            ->with(['genres', 'country', 'director']);

        // Filter by genres
        if ($request->has('genres')) {
            $query->whereHas('genres', function($q) use ($request) {
                $q->whereIn('genres.id', $request->genres);
            });
        }

        // Filter by countries
        if ($request->has('countries')) {
            $query->whereIn('country_id', $request->countries);
        }

        // Filter by years
        if ($request->has('years')) {
            $query->whereIn('release_year', $request->years);
        }

        // Filter by qualities
        if ($request->has('qualities')) {
            $query->whereIn('quality', $request->qualities);
        }

        // Sort
        switch ($request->sort) {
            case 'rating':
                $query->orderBy('imdb_rating', 'desc');
                break;
            case 'views':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $movies = $query->paginate(24);

        // Update pagination URLs
        $movies->withPath('/series');
        $movies->appends($request->except('page'));

        // Log the query and results
        // Log::info('Series Movies Query:', [
        //     'params' => $request->all(),
        //     'sql' => $query->toSql(),
        //     'bindings' => $query->getBindings(),
        //     'count' => $movies->total()
        // ]);

        return response()->json($movies);
    }

    public function getFeaturedMovies()
    {
        $movies = Movie::where('is_featured', true)
            ->with(['genres', 'country', 'director'])
            ->latest()
            ->take(6)
            ->get();

        return response()->json($movies);
    }

    public function getLatestMovies()
    {
        $movies = Movie::with(['genres', 'country', 'director'])
            ->latest()
            ->take(12)
            ->get();

        return response()->json($movies);
    }

    public function top10Series()
    {
        $movies = Movie::with(['genres', 'actors'])
            ->where('is_series', 1)
            ->orderByDesc('imdb_rating')
            ->take(10)
            ->get();
        return response()->json($movies);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $perPage = $request->get('per_page', 20);
        $page = $request->get('page', 1);

        $movies = Movie::where('status', true)
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('sub_title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhereHas('actors', function($actorQuery) use ($query) {
                      $actorQuery->where('name', 'LIKE', "%{$query}%")
                                ->orWhere('slug', 'LIKE', "%{$query}%");
                  });
            })
            ->with(['genres', 'country', 'director', 'actors'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $movies->items(),
            'total' => $movies->total(),
            'current_page' => $movies->currentPage(),
            'per_page' => $movies->perPage(),
            'last_page' => $movies->lastPage()
        ]);
    }
} 