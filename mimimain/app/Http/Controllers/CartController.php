<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classification;    // ← đổi từ Product sang Classification

class CartController extends Controller
{
    // Hiển thị giỏ hàng (không cần thay đổi)
    public function index()
    {
        $cart = session('cart', []);
        $total = array_reduce($cart, fn($sum, $item) =>
            $sum + ($item['price'] * $item['quantity']), 0
        );
        return view('cart.index', compact('cart', 'total'));
    }

    // Thêm vào giỏ
    public function add(Request $request, $id)
    {
        // Lấy “sản phẩm” từ table classifications
        $cls = Classification::findOrFail($id);

        $cart = session('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            // Nếu table classifications của bạn có cột img, name, và (nếu có) price
            $cart[$id] = [
                'name'     => $cls->name,
                'price'    => $cls->price ?? 0,     // nếu không có price, mặc định 0
                'quantity' => 1,
                'image'    => $cls->img ?? null,   // hoặc $cls->image tuỳ bạn đặt column
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', "✅ Đã thêm “{$cls->name}” vào giỏ hàng!");
    }

    // Xóa khỏi giỏ (không cần thay đổi)
    public function remove(Request $request, $id)
    {
        $cart = session('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session(['cart' => $cart]);
        }
        return back()->with('success', '🗑️ Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}
