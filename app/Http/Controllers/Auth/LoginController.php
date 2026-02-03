<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;           

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Chuẩn hóa SĐT (loại bỏ ký tự không phải số)
        $request->merge([
            'phone' => preg_replace('/\D+/', '', (string) $request->input('phone')),
        ]);

        // 1) Validate input
        $credentials = $request->validate([
            'phone'    => ['required', 'regex:/^0\d{9}$/'],
            'password' => ['required'],
        ]);

        // 2) Thử đăng nhập
        if (Auth::attempt([
            'phone' => $credentials['phone'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // 3) Xử lý lỗi: kiểm tra xem số điện thoại có trong DB hay không
        $user = User::where('phone', $request->phone)->first();
        if (! $user) {
            return back()
                ->withErrors(['phone' => 'Chưa có tài khoản gắn với số điện thoại này'])
                ->withInput($request->only('phone'));
        }

        // 4) SĐT đúng, nghĩa là chỉ có thể sai mật khẩu
        return back()
            ->withErrors(['password' => 'Mật khẩu không đúng'])
            ->withInput($request->only('phone'));
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    protected function authenticated(Request $request, $user)
    {
        // Gọi mergeDBCartIntoSession từ CartController
        (new CartController())->mergeDBCartIntoSession();
    }
}
