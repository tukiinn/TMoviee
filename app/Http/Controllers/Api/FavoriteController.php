<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Movie;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // Lấy danh sách phim yêu thích của user
    public function index(Request $request)
    {
        $user = $request->user();
        $movies = $user->favoriteMovies()->with(['genres', 'country', 'director'])->get();
        return response()->json($movies);
    }

    // Thêm phim vào yêu thích
    public function store(Request $request, $movieId)
    {
        $user = $request->user();
        $movie = Movie::findOrFail($movieId);
        $user->favoriteMovies()->syncWithoutDetaching([$movie->id]);
        return response()->json(['message' => 'Đã thêm vào danh sách yêu thích']);
    }

    // Xoá phim khỏi yêu thích
    public function destroy(Request $request, $movieId)
    {
        $user = $request->user();
        $movie = Movie::findOrFail($movieId);
        $user->favoriteMovies()->detach($movie->id);
        return response()->json(['message' => 'Đã xoá khỏi danh sách yêu thích']);
    }

    // Kiểm tra phim đã yêu thích chưa
    public function isFavorite(Request $request, $movieId)
    {
        $user = $request->user();
        $isFavorite = $user->favoriteMovies()->where('movie_id', $movieId)->exists();
        return response()->json(['favorite' => $isFavorite]);
    }
}
