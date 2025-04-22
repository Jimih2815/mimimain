<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Model sản phẩm của bạn

class CartController extends Controller
{
    // Hiển thị trang giỏ hàng
    public function index()
    {
        $cart = session('cart', []);
        $total = array_reduce($cart, fn($sum, $item) => $sum + $item['price'] * $item['quantity'], 0);
        return view('cart.index', compact('cart', 'total'));
    }

    // Thêm sản phẩm vào giỏ
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $cart = session('cart', []);

        if (isset($cart[$id])) {
            // Nếu đã có rồi thì tăng quantity
            $cart[$id]['quantity']++;
        } else {
            // Lần đầu thêm, lưu thông tin cần thiết
            $cart[$id] = [
                "name"     => $product->name,
                "price"    => $product->price,
                "quantity" => 1,
                "image"    => $product->image_url ?? null, // tuỳ bạn có trường hình ảnh không
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', '✅ Đã thêm “' . $product->name . '” vào giỏ hàng!');
    }

    // Xóa sản phẩm khỏi giỏ
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
