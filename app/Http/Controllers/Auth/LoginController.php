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
        // 1) Validate input
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
        ]);

        // 2) Thử đăng nhập
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // 3) Xử lý lỗi: kiểm tra xem email có trong DB hay không
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()
                ->withErrors(['email' => 'Chưa có tài khoản gắn với email này'])
                ->withInput($request->only('email'));
        }

        // 4) Email đúng, nghĩa là chỉ có thể sai mật khẩu
        return back()
            ->withErrors(['password' => 'Mật khẩu không đúng'])
            ->withInput($request->only('email'));
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
