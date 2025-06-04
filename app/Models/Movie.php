<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'sub_title',
        'description',
        'poster',
        'banner',
        'trailer_url',
        'video_url',
        'duration',
        'release_year',
        'quality',
        'age_rating',
        'imdb_rating',
        'is_series',
        'total_episodes',
        'current_episode',
        'is_featured',
        'is_latest',
        'status',
        'country_id',
        'director_id',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'movie_actor')->withPivot('character_name');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function director()
    {
        return $this->belongsTo(Director::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(\App\Models\User::class, 'favorites');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
} 