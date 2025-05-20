<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OptionValue;
use Illuminate\Http\Request;
use App\Services\SyncsCart;
use Jenssegers\Agent\Agent;  

class CartController extends Controller
{
    use SyncsCart;

    /**
     * Hiển thị giỏ hàng
     */
    public function index()
{
    /* 1. Ghép cart DB -> session như cũ */
    $this->mergeDBCartIntoSession();
    $cart = session('cart', []);

    // Gộp entry trùng key
    $merged = [];
    foreach ($cart as $entry) {
        $key = md5($entry['product_id'].'|'.json_encode($entry['options'] ?? []));
        $merged[$key]['quantity'] = ($merged[$key]['quantity'] ?? 0) + $entry['quantity'];
        $merged[$key] = array_replace($merged[$key] ?? [], $entry);
    }
    $cart = $merged;

    // Thêm slug còn thiếu
    foreach ($cart as $key => $item) {
        if (empty($item['slug'])) {
            $cart[$key]['slug'] = Product::find($item['product_id'])->slug;
        }
    }
    session(['cart' => $cart]);

    /* 2. Tính tổng tiền */
    $total = array_reduce($cart, fn($s,$i) => $s + $i['price'] * $i['quantity'], 0);

    /* 3. Chọn view dựa theo thiết bị */
    $agent = new Agent;
    $view  = $agent->isMobile()
            ? 'cart.index-mobile'   // file mới: copy từ index.blade và chỉnh CSS
            : 'cart.index';

    return view($view, compact('cart','total'));
}

    /**
     * Thêm sản phẩm vào giỏ (có options & tính giá base+extra)
     */
    /**
 * Thêm sản phẩm vào giỏ (có options & tính giá base+extra)
 */
/**
 * Thêm sản phẩm vào giỏ (có options & tính giá base+extra)
 */
public function add(Request $request, $id)
{
    $product = Product::with('optionValues')->findOrFail($id);
    $chosen  = $request->input('options', []);

    // 1) Tính tổng extra price
    $extra = OptionValue::whereIn('id', array_values($chosen))
                        ->sum('extra_price');
    $price = $product->base_price + $extra;

    // 2) Chọn ảnh cho item
    $imagePath = $product->img;
    if (!empty($chosen)) {
        $firstValId = array_values($chosen)[0];
        $optVal     = OptionValue::find($firstValId);
        if ($optVal && $optVal->option_img) {
            $imagePath = $optVal->option_img;
        }
    }
    $imageUrl = asset('storage/' . $imagePath);

    // 3) Thêm / cập nhật session cart
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
            'image'      => $imagePath,
            'slug'       => $product->slug,
        ];
    }

    session(['cart' => $cart]);
    $this->syncCartToDB($cart);

    // 4) AJAX response: thêm always trả về trường `item`
    if ($request->ajax()) {
        $totalItems = array_sum(array_column($cart, 'quantity'));
        $imageUrl   = asset('storage/' . $imagePath);
    
        return response()->json([
            'success'      => true,
            'message'      => "Đã thêm “{$product->name}” vào giỏ hàng!",
            'total_items'  => $totalItems,
            'item'         => [
                'key'      => $key,
                'name'     => $product->name,
                'price'    => $price,
                'quantity' => $cart[$key]['quantity'],  // số lượng thực
                'image'    => $imageUrl,
            ],
        ]);
    }

    // 5) Fallback non-AJAX
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
        if ($request->ajax()) {
            return response()->json(['success' => false], 404);
        }
        return back()->with('error', 'Mục không tồn tại trong giỏ hàng.');
    }

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

    // **Nếu AJAX, trả về JSON chỉ gồm success + quantity**
    if ($request->ajax()) {
        return response()->json([
            'success'  => true,
            'quantity' => $cart[$key]['quantity'] ?? 0,
        ]);
    }

    return back()->with('success', 'Cập nhật giỏ hàng thành công.');
}
   public function menuMobile(Request $request)
{
    // Nếu user đã login, merge DB→session để bắt kịp mọi thay đổi (add, update, delete)
    if (auth()->check()) {
        $this->mergeDBCartIntoSession();
    }

    $cart  = session('cart', []);
    $count = array_sum(array_column($cart, 'quantity'));

    return view('partials.mobile-cart-menu', compact('cart', 'count'));
}
}
