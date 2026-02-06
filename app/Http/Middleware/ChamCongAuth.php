<?php

namespace App\Http\Middleware;

use App\Models\ChamCong\ChamCongUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ChamCongAuth
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('chamcong_user_id')) {
            return $next($request);
        }

        $token = $request->cookie('remember_token');
        if ($token) {
            $user = ChamCongUser::where('remember_token', $token)->first();
            if ($user) {
                $request->session()->put('chamcong_user_id', $user->id);
                $request->session()->put('chamcong_username', $user->username);
                return $next($request);
            }

            Cookie::queue(Cookie::forget('remember_token'));
        }

        return redirect()->route('chamcong.login');
    }
}
