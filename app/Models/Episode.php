<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = [
        'movie_id', 'season', 'episode_number', 'title', 'duration', 'video_url', 'description', 'thumbnail', 'status'
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
