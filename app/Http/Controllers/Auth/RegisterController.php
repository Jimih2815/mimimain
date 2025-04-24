<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Form đăng ký  ─ GET /register
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký ─ POST /register
     * ⚠ Hàm tên **register** đúng như route gốc của Laravel.
     */
    public function register(Request $request)
    {
        /* 1. Validate */
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                Password::min(8)->letters()->mixedCase()->numbers(), // tuỳ chỉnh rule
                'confirmed',                                         // cần field password_confirmation
            ],
        ]);

        /* 2. Tạo user */
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        /* 3. Bắn event Registered (nếu dùng verify email, listener…) */
        event(new Registered($user));

        /* 4. Tự đăng nhập */
        auth()->login($user);

        /* 5. Redirect */
        return redirect('/')->with('status', 'Đăng ký thành công!');
    }
}
