<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMembership extends Model
{
    protected $fillable = [
        'user_id',
        'membership_package_id',
        'start_at',
        'end_at',
        'status',
        'payment_id',
        'amount_paid',
        'payment_method'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'amount_paid' => 'float'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(MembershipPackage::class, 'membership_package_id');
    }

    // Lấy gói có quyền cao nhất đang active
    public static function getHighestActivePackage($userId)
    {
        return self::where('user_id', $userId)
            ->where('status', 'active')
            ->where('end_at', '>', now())
            ->with('package')
            ->orderByDesc('package.price')
            ->first();
    }

    // Lấy tất cả gói đang active
    public static function getActivePackages($userId)
    {
        return self::where('user_id', $userId)
            ->where('status', 'active')
            ->where('end_at', '>', now())
            ->with('package')
            ->orderByDesc('package.price')
            ->get();
    }

    // Kiểm tra xem user có quyền của package không
    public static function hasPackageAccess($userId, $packageId)
    {
        $highestPackage = self::getHighestActivePackage($userId);
        if (!$highestPackage) return false;

        $targetPackage = MembershipPackage::find($packageId);
        if (!$targetPackage) return false;

        return $highestPackage->package->price >= $targetPackage->price;
    }
}
