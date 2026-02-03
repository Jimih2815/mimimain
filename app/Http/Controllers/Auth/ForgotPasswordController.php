<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Hiển thị form quên mật khẩu
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Gửi link reset qua email dựa vào số điện thoại
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Chuẩn hóa SĐT
        $request->merge([
            'phone' => preg_replace('/\D+/', '', (string) $request->input('phone')),
        ]);

        $data = $request->validate([
            'phone' => ['required', 'regex:/^0\d{9}$/'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);
        $emailInput = strtolower(trim((string) ($data['email'] ?? '')));

        $user = User::where('phone', $data['phone'])->first();
        if (! $user) {
            return back()
                ->withErrors(['phone' => 'Số điện thoại này chưa được đăng ký.'])
                ->withInput($request->only('phone', 'email'));
        }

        // Trường hợp 1: user đã có email -> bắt buộc nhập đúng email đó
        if (!empty($user->email)) {
            $userEmail = strtolower(trim((string) $user->email));
            if ($emailInput === '') {
                return back()
                    ->withErrors(['email' => 'Vui lòng nhập email gắn với số điện thoại này.'])
                    ->withInput($request->only('phone', 'email'));
            }
            if ($emailInput !== $userEmail) {
                return back()
                    ->withErrors(['email' => 'Sai email gắn với số điện thoại này.'])
                    ->withInput($request->only('phone', 'email'));
            }

            $emailToSend = $user->email;
        } else {
            // Trường hợp 3: chưa có email -> yêu cầu nhập và gắn vào tài khoản
            if ($emailInput === '') {
                return back()
                    ->withErrors(['email' => 'Vui lòng nhập email để nhận link đặt lại mật khẩu.'])
                    ->withInput($request->only('phone', 'email'));
            }

            $emailExists = User::where('email', $emailInput)
                               ->where('id', '!=', $user->id)
                               ->exists();
            if ($emailExists) {
                return back()
                    ->withErrors(['email' => 'Email này đã được sử dụng cho tài khoản khác.'])
                    ->withInput($request->only('phone', 'email'));
            }

            $user->email = $emailInput;
            $user->save();
            $emailToSend = $emailInput;
        }

        $status = Password::sendResetLink(['email' => $emailToSend]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Đã gửi link khôi phục mật khẩu vào email của bạn.')
            : back()->withInput($request->only('phone', 'email'))
                    ->withErrors(['email' => __($status)]);
    }
}
