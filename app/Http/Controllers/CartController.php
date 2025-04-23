<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OptionValue;
use Illuminate\Http\Request;
use App\Services\SyncsCart;

class CartController extends Controller
{
    use SyncsCart;

    /**
     * Hiển thị giỏ hàng
     */
    public function index()
    {
        $this->mergeDBCartIntoSession();
        $cart  = session('cart', []);
        $total = array_reduce(
            $cart,
            fn($sum, $item) => $sum + $item['price'] * $item['quantity'],
            0
        );
        return view('cart.index', compact('cart', 'total'));
    }

    /**
     * Thêm sản phẩm vào giỏ (có options & tính giá base+extra)
     */
    public function add(Request $request, $id)
    {
        $product = Product::with('optionValues')->findOrFail($id);
        $chosen  = $request->input('options', []);
        $extra   = OptionValue::whereIn('id', array_values($chosen))
                              ->sum('extra_price');
        $price   = $product->base_price + $extra;

        $cart = session('cart', []);
        $key  = md5($id . '|' . json_encode($chosen));

        if (isset($cart[$key])) {
            $cart[$key]['quantity']++;
        } else {
            $cart[$key] = [
                'product_id' => $id,
                'quantity'   => 1,
                'price'      => $price,
                'options'    => $chosen,
                'name'       => $product->name,
                'image'      => $product->img,
            ];
        }

        session(['cart' => $cart]);
        $this->syncCartToDB($cart);

        return back()->with('success', "Đã thêm “{$product->name}” vào giỏ hàng!");
    }

    /**
     * Xóa mục khỏi giỏ (key là MD5 hash)
     */
    public function remove(Request $request, $key)
    {
        $cart = session('cart', []);
        if (isset($cart[$key])) {
            unset($cart[$key]);
            session(['cart' => $cart]);
            $this->syncCartToDB($cart);
            return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        }
        return back()->with('error', 'Mục không tồn tại trong giỏ hàng.');
    }
}
