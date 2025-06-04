<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Director extends Model
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

    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class);
    }
} 