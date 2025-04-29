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
        // Lấy mảng ID sản phẩm yêu thích từ session
        $favIds = session('favorites', []);

        // Lấy sản phẩm với quan hệ optionValues→type, phân trang 12/item
        $products = Product::with('optionValues.type')
                           ->whereIn('id', $favIds)
                           ->paginate(12);

        // Lấy danh sách tất cả OptionType cùng values để view hiển thị tuỳ chọn
        $optionTypes = OptionType::with('values')->get();

        // Trả về view cùng 2 biến products và optionTypes
        return view('favorites.index', compact('products', 'optionTypes'));
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
