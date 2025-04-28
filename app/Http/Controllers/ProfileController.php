<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Hiển thị form chỉnh sửa hồ sơ
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Xử lý cập nhật thông tin hồ sơ
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->update($data);

        return redirect()
            ->route('profile.edit')
            ->with('status', 'Cập nhật thành công!');
    }

    /**
     * Xử lý đổi mật khẩu
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password'      => ['required'],
            'password'              => ['required', 'confirmed'],
        ]);

        // Kiểm tra mật khẩu hiện tại
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Mật khẩu hiện tại không đúng.',
            ]);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('password_status', 'Đổi mật khẩu thành công!');
    }
}
