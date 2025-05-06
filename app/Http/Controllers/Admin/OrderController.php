<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;                 
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    
public function index(Request $request)
{
    // Lấy từ khoá tìm kiếm (nếu có)
    $search = $request->input('q');

    // 1) Khởi tạo query với eager-load user và items.product
    $query = Order::with(['user', 'items.product'])
                  ->latest();

    // 2) Nếu có search thì filter theo fullname, address, phone, order_code,
    //    tracking_number và email của user liên quan
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('fullname',         'like', "%{$search}%")
              ->orWhere('address',        'like', "%{$search}%")
              ->orWhere('phone',          'like', "%{$search}%")
              ->orWhere('order_code',     'like', "%{$search}%")
              ->orWhere('tracking_number','like', "%{$search}%")
              ->orWhereHas('user', function($q2) use ($search) {
                  $q2->where('email', 'like', "%{$search}%");
              });
        });
    }

    // 3) Paginate 20 bản ghi/trang, và đính query string để giữ param q
    $orders = $query->paginate(20)
                    ->appends(['q' => $search]);

    // 4) Trả về view kèm biến $orders và $search để view show lại nhu cầu filter
    return view('admin.orders.index', compact('orders', 'search'));
}
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'tracking_number' => ['nullable','string','max:255'],
            'status'          => ['nullable', Rule::in(['pending','shipping','done'])],
        ]);

        // Nếu admin điền tracking_number lần đầu, chuyển trạng thái sang shipping
        if (array_key_exists('tracking_number', $data)) {
            $order->tracking_number = $data['tracking_number'];
            if ($data['tracking_number'] && $order->status === 'pending') {
                $order->status = 'shipping';
            }
        }
        // Nếu admin chỉnh thủ công status
        if (array_key_exists('status', $data)) {
            $order->status = $data['status'];
        }

        $order->save();
        return back();
    }
    
}
