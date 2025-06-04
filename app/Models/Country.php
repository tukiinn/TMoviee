<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'code',
        'status'
    ];

    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class);
    }
} 