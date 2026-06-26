<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Nếu chưa đăng nhập
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Nếu đã đăng nhập nhưng không phải admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập trang admin.');
        }

        return $next($request);
    }
}
