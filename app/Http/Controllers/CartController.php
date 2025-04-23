<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\SyncsCart;

class CartController extends Controller
{
    use SyncsCart;

    /* ------------ hiển thị giỏ ------------ */
    public function index()
    {
        $this->mergeDBCartIntoSession();          // tự khôi phục nếu đã login
        $cart  = session('cart', []);

        $total = array_reduce($cart, fn ($s, $i) =>
            $s + $i['price'] * $i['quantity'], 0);

        return view('cart.index', compact('cart', 'total'));
    }

    /* ------------ thêm ------------ */
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart    = session('cart', []);

        // ----- gán mặc định rồi tăng -----
        $cart[$id]['quantity'] ??= 0;
        $cart[$id]['quantity']++;

        // ----- các field còn lại chỉ gán 1 lần -----
        $cart[$id]['name']   ??= $product->name;
        $cart[$id]['price']  ??= $product->base_price;
        $cart[$id]['image']  ??= $product->img;

        session(['cart' => $cart]);
        $this->syncCartToDB($cart);

        return back()->with('success', "Đã thêm “{$product->name}” vào giỏ!");
    }

    /* ------------ xoá ------------ */
    public function remove(Request $request, $id)
    {
        $cart = session('cart', []);
        unset($cart[$id]);

        session(['cart' => $cart]);
        $this->syncCartToDB($cart);

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}
