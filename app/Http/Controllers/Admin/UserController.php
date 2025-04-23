<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách user
     */
    public function index()
    {
        $users = User::orderBy('created_at','desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Hiển thị chi tiết 1 user + đơn hàng của họ
     */
    public function show(User $user)
    {
        // bây giờ có user_id trên orders, chạy ok
        $orders = $user->orders()
                       ->with('items.product')
                       ->orderBy('created_at', 'desc')
                       ->get();

        return view('admin.users.show', compact('user', 'orders'));
    }

    /**
     * Reset mật khẩu về 123456789
     */
    public function resetPassword(User $user)
    {
        $user->password = bcrypt('123456789');
        $user->save();
        return back()->with('success', "Đã reset mật khẩu của {$user->email} về mặc định.");
    }

    /**
     * Xóa user
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
                         ->with('success','Đã xóa user.');
    }
}
