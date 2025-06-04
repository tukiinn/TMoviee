<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Actor extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'biography',
        'avatar',
        'birth_date',
        'birth_place',
        'status'
    ];

    protected $casts = [
        'birth_date' => 'date'
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_actor')->withPivot('character_name');
    }
} 