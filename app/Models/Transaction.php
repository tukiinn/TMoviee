<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'duration',
        'amount',
        'status',
        'payment_method',
        'payment_info',
        'completed_at'
    ];

    protected $casts = [
        'amount' => 'float',
        'completed_at' => 'datetime',
        'payment_info' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(MembershipPackage::class, 'package_id');
    }
} 