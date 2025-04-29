<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OptionType;

class FavoriteController extends Controller
{
    /**
     * GET /favorites
     * Hiển thị trang Yêu Thích, truyền cả products và optionTypes cho view
     */
    public function index(Request $request)
    {
        $favorites = session('favorites', []);
        
        // Thay vì paginate(), dùng get() để lấy hết
        $products = \App\Models\Product::whereIn('id', $favorites)
                    ->with('optionValues.type')  // nếu bạn cần eager-load
                    ->get();

        return view('favorites.index', compact('products'));
    }

    /**
     * POST /favorites/toggle/{product}
     * Thêm hoặc bỏ sản phẩm khỏi danh sách yêu thích
     */
    public function toggle(Request $request, Product $product)
    {
        $favorites = session('favorites', []);

        if (in_array($product->id, $favorites)) {
            // Bỏ favorite
            $favorites = array_filter($favorites, fn($id) => $id != $product->id);
            $added = false;
        } else {
            // Thêm favorite
            $favorites[] = $product->id;
            $added = true;
        }

        // Cập nhật session
        session(['favorites' => array_values($favorites)]);

        return response()->json(['added' => $added]);
    }
}
