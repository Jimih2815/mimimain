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
    
        // 1) Lấy cart từ session
        $cart = session('cart', []);
    
        // 2) Với mỗi item, bổ sung slug nếu chưa có
        foreach ($cart as $key => $item) {
            if (! isset($item['slug'])) {
                // Lấy model Product để đọc slug
                $prod = Product::find($item['product_id']);
                $cart[$key]['slug'] = $prod->slug;
            }
        }
        // Viết lại cart vào session (tuỳ chọn, để giữ persist)
        session(['cart' => $cart]);
    
        // 3) Tính tổng như cũ
        $total = array_reduce(
            $cart,
            fn($sum, $item) => $sum + $item['price'] * $item['quantity'],
            0
        );
    
        return view('cart.index', compact('cart','total'));
    }

    /**
     * Thêm sản phẩm vào giỏ (có options & tính giá base+extra)
     */
    public function add(Request $request, $id)
{
    $product = Product::with('optionValues')->findOrFail($id);
    $chosen  = $request->input('options', []);
    // Tính tổng extra price từ các option đã chọn
    $extra = OptionValue::whereIn('id', array_values($chosen))
                        ->sum('extra_price');
    $price = $product->base_price + $extra;

    // --- Xác định image cho cart item ---
    // Mặc định dùng ảnh chính
    $imagePath = $product->img;
    if (!empty($chosen)) {
        // Lấy option đầu tiên trong mảng chosen
        $firstValId = array_values($chosen)[0];
        $optVal = OptionValue::find($firstValId);
        // Nếu option có ảnh tùy chọn, dùng nó
        if ($optVal && $optVal->option_img) {
            $imagePath = $optVal->option_img;
        }
    }

    // --- Thêm vào session cart ---
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
}
