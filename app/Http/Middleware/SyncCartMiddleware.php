<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SyncsCart;

class SyncCartMiddleware
{
    use SyncsCart;

    /**
     * Mỗi request nếu đã login thì merge cart từ DB → session
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $this->mergeDBCartIntoSession();
        }

        return $next($request);
    }
}
