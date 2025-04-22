<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // Hiển thị trang giỏ hàng
    public function index()
    {
        $cart  = session('cart', []);
        $total = array_reduce($cart, fn($sum, $item) =>
            $sum + ($item['price'] * $item['quantity']), 0
        );
        return view('cart.index', compact('cart', 'total'));
    }

    // Thêm sản phẩm vào giỏ
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart    = session('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name'     => $product->name,
                'price'    => $product->base_price,
                'quantity' => 1,
                'image'    => $product->img,
            ];
        }

        session(['cart' => $cart]);
        return back()->with('success', "Đã thêm “{$product->name}” vào giỏ hàng!");
    }

    // Xóa sản phẩm khỏi giỏ
    public function remove(Request $request, $id)
    {
        $cart = session('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session(['cart' => $cart]);
        }
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}
