<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index()
    {
        return Genre::where('status', true)->get();
    }

    public function show($slug)
    {
        return Genre::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();
    }
} 