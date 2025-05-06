<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')
                 ->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
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
