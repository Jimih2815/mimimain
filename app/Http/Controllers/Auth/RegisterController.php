<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Hiển thị form đăng ký — GET /register
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký — POST /register
     */
    public function register(Request $request)
    {
        // 1. Validate chỉ yêu cầu và confirmed
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed'],
        ]);

        // 2. Tạo user
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // 3. Event nếu có verify
        event(new Registered($user));

        // 4. Tự động đăng nhập
        auth()->login($user);

        // 5. Redirect
        return redirect('/')->with('status', 'Đăng ký thành công!');
    }
}
