<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Lấy thông tin user hiện tại
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // Cập nhật thông tin cá nhân
    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'current_password' => 'required_with:password|string',
            'gender' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if (isset($data['password'])) {
            // Kiểm tra mật khẩu cũ
            if (!Hash::check($data['current_password'], $user->password)) {
                return response()->json(['message' => 'Mật khẩu hiện tại không đúng!'], 422);
            }
            $user->password = bcrypt($data['password']);
        }
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }
        if (isset($data['email'])) {
            $user->email = $data['email'];
        }
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = uniqid('avatar_') . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('images/avatars'), $avatarName);
            $user->avatar = '/images/avatars/' . $avatarName;
        }
        if (isset($data['gender'])) {
            $user->gender = $data['gender'];
        }
        $user->save();
        return response()->json(['message' => 'Cập nhật thông tin thành công!', 'user' => $user]);
    }

    // Lấy thông tin membership hiện tại của user
    public function membership(Request $request)
    {
        $user = $request->user();
        $membership = \App\Models\UserMembership::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_at', '>', now())
            ->with('package')
            ->first();

        if (!$membership) {
            return response()->json(['active' => false]);
        }

        $daysLeft = now()->diffInDays($membership->end_at, false);
        $daysLeft = max(0, (int) $daysLeft);
        return response()->json([
            'active' => true,
            'package' => $membership->package,
            'start_at' => $membership->start_at,
            'end_at' => $membership->end_at,
            'days_left' => $daysLeft
        ]);
    }
}
