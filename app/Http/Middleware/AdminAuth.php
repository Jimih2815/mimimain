<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Cho phép đi vào trang login / submit login
        if ($request->is('admin/login') || $request->is('admin/login/*')) {
            return $next($request);
        }

        // Check đã login admin chưa
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
