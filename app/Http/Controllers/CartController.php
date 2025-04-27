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

    if (! isset($cart[$key])) {
        return back()->with('error', 'Mục không tồn tại trong giỏ hàng.');
    }

    // Nếu còn >1 thì giảm 1, ngược lại xóa hẳn
    if ($cart[$key]['quantity'] > 1) {
        $cart[$key]['quantity']--;
    } else {
        unset($cart[$key]);
    }

    session(['cart' => $cart]);
    $this->syncCartToDB($cart);

    return back()->with('success', 'Cập nhật giỏ hàng thành công.');
}

public function update(Request $request, $key)
{
   $cart = session('cart', []);

   if (! isset($cart[$key])) {
       return back()->with('error', 'Mục không tồn tại trong giỏ hàng.');
   }

   // Lấy action từ form: 'inc' hoặc 'dec'
   $action = $request->input('action');

   if ($action === 'inc') {
       $cart[$key]['quantity']++;
   } elseif ($action === 'dec') {
       if ($cart[$key]['quantity'] > 1) {
           $cart[$key]['quantity']--;
       } else {
           unset($cart[$key]);
       }
   }

   session(['cart' => $cart]);
   $this->syncCartToDB($cart);

   return back()->with('success', 'Cập nhật giỏ hàng thành công.');
}
}
