<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;    
use Illuminate\Support\Str; 
use App\Models\Order;
use App\Models\OrderNote;



class ProfileController extends Controller
{
    /**
     * Hiển thị form chỉnh sửa hồ sơ
     */
    public function edit()
{
    // 1) Lấy user & orders
    $user = Auth::user();
    $orders = $user->orders()
    ->with('items.product','notes.user')
    // Đặt tất cả những order có status = 'cancelled' về cuối (false = 0, true = 1)
    ->orderByRaw("CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END ASC")
    // Trong mỗi nhóm (chưa hủy / đã hủy), sort theo created_at desc
    ->orderBy('created_at','desc')
    ->paginate(10);



    // 2) Lấy và sắp xếp helpRequests giống logic trong Blade
    $orderedRequests = $user->helpRequests
        ->sort(function($a, $b) {
            $aDone = $a->status === 'Hoàn thành';
            $bDone = $b->status === 'Hoàn thành';
            if ($aDone !== $bDone) {
                return $aDone <=> $bDone;
            }
            if ($aDone) {
                return $b->created_at->timestamp <=> $a->created_at->timestamp;
            }
            return $a->created_at->timestamp <=> $b->created_at->timestamp;
        })
        ->values();

    // 3) Phát hiện mobile vs desktop
    $agent = new Agent;
    $view  = $agent->isMobile()
           ? 'profile.edit-mobile'
           : 'profile.edit';

    // 4) Trả về view với đủ biến
    return view($view, compact('user', 'orders', 'orderedRequests'));
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
public function cancelOrder(\App\Models\Order $order)
{
    if ($order->user_id !== auth()->id()) {
        abort(403);
    }

    // flash key riêng để khỏi lẫn với profile-update
    $flashKey = 'order_status';

    if ($order->status === 'pending') {
        $order->status = 'cancelled';
        $order->save();

        // redirect về /profile#orders với flash order_status
        return redirect()
            ->to(route('profile.edit') . '#orders')
            ->with($flashKey, 'Đơn hàng đã được hủy thành công.');
    }

    return redirect()
        ->to(route('profile.edit') . '#orders')
        ->with($flashKey, 'Đơn hàng đã được bàn giao cho Shipper - không thể hủy đơn');
}

public function updateNote(Request $req, Order $order)
{
    abort_if($order->user_id !== auth()->id(),403);
    $req->validate(['note'=>'nullable|string']);
    $order->notes()->create([
        'user_id'  => auth()->id(),
        'is_admin' => false,
        'message'  => $req->note,
    ]);
    return redirect()->to(route('profile.edit').'#orders')
                     ->with('note_status','Cập nhật ghi chú thành công.');
}

}
