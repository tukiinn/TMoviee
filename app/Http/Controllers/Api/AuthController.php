<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Email hoặc mật khẩu không đúng!'], 401);
        }
        $user = Auth::user();
        $token = $user->createToken('api_token')->plainTextToken;

        $redirectUrl = null;
        if ($user->role === 'admin') {
            $redirectUrl = '/admin';
            \Illuminate\Support\Facades\Log::info('Admin login detected', [
                'user_id' => $user->id,
                'email' => $user->email,
                'redirect_url' => $redirectUrl
            ]);
        }

        $response = [
            'token' => $token,
            'user'  => $user,
            'redirect_url' => $redirectUrl
        ];

        \Illuminate\Support\Facades\Log::info('Login response', $response);
        return response()->json($response);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Đăng xuất thành công!']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user
        ]);
    }
}
