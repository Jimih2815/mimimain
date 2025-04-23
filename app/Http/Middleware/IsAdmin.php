<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Giả sử bạn có cột is_admin trong users
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Bạn không có quyền truy cập.');
        }
        return $next($request);
    }
}
