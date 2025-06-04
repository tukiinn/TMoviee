<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'duration_days',
        'features',
        'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function users()
    {
        return $this->hasMany(UserMembership::class);
    }
}
