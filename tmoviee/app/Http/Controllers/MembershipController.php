<?php

namespace App\Http\Controllers;

use App\Models\UserMembership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function getUserMembership(Request $request)
    {
        $user = $request->user();
        
        // Lấy tất cả gói đang active với relationship package
        $activePackages = UserMembership::with('package')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_at', '>', now())
            ->orderBy('membership_package_id', 'desc')
            ->get();
        
        // Debug: Kiểm tra dữ liệu trực tiếp
        $debug = [
            'user_id' => $user->id,
            'query' => UserMembership::with('package')
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->where('end_at', '>', now())
                ->toSql(),
            'bindings' => [
                'user_id' => $user->id,
                'now' => now()
            ],
            'active_packages_count' => $activePackages->count(),
            'active_packages' => $activePackages->map(function($pkg) {
                return [
                    'id' => $pkg->id,
                    'user_id' => $pkg->user_id,
                    'package_id' => $pkg->membership_package_id,
                    'start_at' => $pkg->start_at,
                    'end_at' => $pkg->end_at,
                    'status' => $pkg->status,
                    'package' => $pkg->package ? [
                        'id' => $pkg->package->id,
                        'name' => $pkg->package->name
                    ] : null
                ];
            })->toArray()
        ];

        // Tính toán thời gian còn lại cho mỗi gói
        $activePackages->transform(function ($membership) {
            $membership->days_left = now()->diffInDays($membership->end_at);
            return $membership;
        });

        // Lấy gói cao cấp nhất (gói có ID cao nhất)
        $highestPackage = $activePackages->sortByDesc('membership_package_id')->first();

        // Chuyển đổi active packages thành mảng đơn giản
        $formattedActivePackages = $activePackages->map(function($pkg) {
            return [
                'id' => $pkg->membership_package_id,
                'name' => $pkg->package->name,
                'description' => $pkg->package->description,
                'price' => $pkg->package->price,
                'features' => $pkg->package->features,
                'start_at' => $pkg->start_at,
                'end_at' => $pkg->end_at,
                'status' => $pkg->status,
                'days_left' => $pkg->days_left,
                'payment_id' => $pkg->payment_id,
                'amount_paid' => $pkg->amount_paid,
                'payment_method' => $pkg->payment_method
            ];
        })->values()->toArray();

        // Tạo response
        $response = [
            'active' => $activePackages->isNotEmpty(),
            'package' => $highestPackage ? [
                'id' => $highestPackage->membership_package_id,
                'name' => $highestPackage->package->name,
                'description' => $highestPackage->package->description,
                'price' => $highestPackage->package->price,
                'features' => $highestPackage->package->features,
                'start_at' => $highestPackage->start_at,
                'end_at' => $highestPackage->end_at,
                'status' => $highestPackage->status,
                'payment_id' => $highestPackage->payment_id,
                'amount_paid' => $highestPackage->amount_paid,
                'payment_method' => $highestPackage->payment_method
            ] : null,
            'days_left' => $highestPackage ? $highestPackage->days_left : 0,
            'end_at' => $highestPackage ? $highestPackage->end_at : null,
            'start_at' => $highestPackage ? $highestPackage->start_at : null,
            'active_packages' => $formattedActivePackages,
            'has_active_membership' => $activePackages->isNotEmpty(),
            '_debug' => $debug
        ];

  

        return response()->json($response);
    }

    // Thêm method mới để kiểm tra dữ liệu
    public function checkMembershipData(Request $request)
    {
        $user = $request->user();
        
        // Lấy tất cả gói membership của user
        $allMemberships = UserMembership::with('package')
            ->where('user_id', $user->id)
            ->get();

        // Lấy các gói đang active
        $activeMemberships = UserMembership::with('package')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_at', '>', now())
            ->get();

        return response()->json([
            'all_memberships' => $allMemberships->map(function($m) {
                return [
                    'id' => $m->id,
                    'user_id' => $m->user_id,
                    'package_id' => $m->membership_package_id,
                    'start_at' => $m->start_at,
                    'end_at' => $m->end_at,
                    'status' => $m->status,
                    'package' => $m->package ? [
                        'id' => $m->package->id,
                        'name' => $m->package->name,
                        'price' => $m->package->price
                    ] : null
                ];
            }),
            'active_memberships' => $activeMemberships->map(function($m) {
                return [
                    'id' => $m->id,
                    'user_id' => $m->user_id,
                    'package_id' => $m->membership_package_id,
                    'start_at' => $m->start_at,
                    'end_at' => $m->end_at,
                    'status' => $m->status,
                    'package' => $m->package ? [
                        'id' => $m->package->id,
                        'name' => $m->package->name,
                        'price' => $m->package->price
                    ] : null
                ];
            })
        ]);
    }
} 