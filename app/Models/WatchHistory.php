<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchHistory extends Model
{
    protected $fillable = [
        'user_id',
        'movie_id',
        'current_time',
        'duration',
        'season',
        'episode',
        'last_watched_at'
    ];

    protected $casts = [
        'current_time' => 'float',
        'duration' => 'float',
        'last_watched_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
} 