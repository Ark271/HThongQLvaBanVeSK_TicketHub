<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Nếu chưa đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Nếu đã đăng nhập nhưng không phải admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập trang admin.');
        }

        return $next($request);
    }
}
