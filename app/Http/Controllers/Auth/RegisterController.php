<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Nơi redirect sau khi đăng ký thành công.
     */
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Hiển thị form đăng ký.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Validator cho form.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'                  => ['required','string','max:255'],
            'email'                 => ['required','string','email','max:255','unique:users'],
            'password'              => ['required','string','min:8','confirmed'],
        ]);
    }

    /**
     * Tạo mới User sau khi validate.
     */
    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
