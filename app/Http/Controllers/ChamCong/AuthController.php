<?php

namespace App\Http\Controllers\ChamCong;

use App\Http\Controllers\Controller;
use App\Models\ChamCong\ChamCongUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->session()->has('chamcong_user_id')) {
            return redirect()->route('chamcong.dashboard');
        }

        return view('chamcong.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required','string'],
            'password' => ['required','string'],
        ]);

        $user = ChamCongUser::where('username', $request->input('username'))->first();
        if ($user && $user->password === $request->input('password')) {
            $request->session()->put('chamcong_user_id', $user->id);
            $request->session()->put('chamcong_username', $user->username);

            if ($request->boolean('remember_me')) {
                $token = bin2hex(random_bytes(32));
                $user->remember_token = $token;
                $user->save();

                Cookie::queue(cookie('remember_token', $token, 60 * 24 * 30, '/'));
            }

            return redirect()->route('chamcong.dashboard');
        }

        return back()->withInput()->with('error', 'Sai tài khoản hoặc mật khẩu!');
    }

    public function logout(Request $request)
    {
        $userId = $request->session()->get('chamcong_user_id');
        if ($userId) {
            ChamCongUser::where('id', $userId)->update(['remember_token' => null]);
        }

        $request->session()->forget(['chamcong_user_id', 'chamcong_username']);
        Cookie::queue(Cookie::forget('remember_token'));

        return redirect()->route('chamcong.login');
    }
}
