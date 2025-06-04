<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use Illuminate\Http\Request;

class ActorController extends Controller
{
    public function index()
    {
        return Actor::where('status', true)->get();
    }

    public function show($slug)
    {
        return Actor::where('slug', $slug)
            ->where('status', true)
            ->with(['movies' => function($query) {
                $query->where('status', true)
                    ->with(['genres', 'country'])
                    ->latest();
            }])
            ->firstOrFail();
    }
} 