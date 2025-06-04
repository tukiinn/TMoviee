<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // Nếu là request API, trả về JSON 401 thay vì redirect
        if ($request->expectsJson()) {
            abort(response()->json(['message' => 'Bạn cần đăng nhập!'], 401));
        }
        // Nếu là web, bạn có thể redirect về trang login nếu muốn
        // return route('login');
    }
} 