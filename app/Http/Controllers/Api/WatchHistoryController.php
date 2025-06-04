<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WatchHistory;
use Illuminate\Support\Facades\Log;

class WatchHistoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'current_time' => 'required|numeric',
            'duration' => 'required|numeric',
            'season' => 'nullable|integer',
            'episode' => 'nullable|integer'
        ]);

        $user = $request->user();
        Log::info('Saving watch history', [
            'user_id' => $user->id,
            'movie_id' => $request->movie_id,
            'current_time' => $request->current_time,
            'duration' => $request->duration
        ]);

        $watchHistory = WatchHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'movie_id' => $request->movie_id,
                'season' => $request->season,
                'episode' => $request->episode
            ],
            [
                'current_time' => $request->current_time,
                'duration' => $request->duration,
                'last_watched_at' => now()
            ]
        );

        return response()->json($watchHistory);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $watchHistory = WatchHistory::with('movie')
            ->where('user_id', $user->id)
            ->orderBy('last_watched_at', 'desc')
            ->get();

        return response()->json($watchHistory);
    }

    public function getMovieProgress(Request $request, $movieId)
    {
        $user = $request->user();
        Log::info('Getting movie progress', [
            'user_id' => $user->id,
            'movie_id' => $movieId
        ]);

        $watchHistory = WatchHistory::where('user_id', $user->id)
            ->where('movie_id', $movieId)
            ->first();

        Log::info('Watch history found', [
            'watch_history' => $watchHistory
        ]);

        if (!$watchHistory) {
            return response()->json([
                'current_time' => 0,
                'duration' => 0,
                'season' => null,
                'episode' => null
            ]);
        }

        return response()->json($watchHistory);
    }
} 