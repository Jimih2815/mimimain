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
        // Chuẩn hóa SĐT (loại bỏ ký tự không phải số)
        $request->merge([
            'phone' => preg_replace('/\D+/', '', (string) $request->input('phone')),
        ]);

        // 1. Validate
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'phone'    => ['required', 'regex:/^0\d{9}$/', 'unique:users,phone'],
            'email'    => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed'],
        ]);

        // 2. Tạo user
        $payload = [
            'name'     => $data['name'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
        ];
        if (!empty($data['email'])) {
            $payload['email'] = $data['email'];
        }
        $user = User::create($payload);

        // 3. Event nếu có verify
        event(new Registered($user));

        // 4. Tự động đăng nhập
        auth()->login($user);

        // 5. Redirect
        return redirect('/')->with('status', 'Đăng ký thành công!');
    }
}
