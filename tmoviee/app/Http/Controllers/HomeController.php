<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use App\Models\Country;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredMovies = Movie::where('is_featured', true)
            ->where('status', true)
            ->with(['genres', 'country', 'director'])
            ->take(12)
            ->get();

        $latestMovies = Movie::where('is_latest', true)
            ->where('status', true)
            ->with(['genres', 'country', 'director'])
            ->take(12)
            ->get();

        $genres = Genre::where('status', true)->get();
        $countries = Country::where('status', true)->get();

        return view('home', compact('featuredMovies', 'latestMovies', 'genres', 'countries'));
    }
} 