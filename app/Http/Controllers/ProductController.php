<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OptionType;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm và search
     */
    public function index(Request $request)
    {
        $q = $request->input('q');
    
        // chỉ load optionValues với type cho mỗi product
        $query = Product::with('optionValues.type');
    
        if ($q) {
            $query->where('name','like',"%{$q}%")
                  ->orWhere('description','like',"%{$q}%");
        }
    
        $products = $query->paginate(12)
                          ->appends(['q'=>$q]);
    
        return view('products.index', compact('products','q'));
    }

    /**
     * Hiển thị chi tiết sản phẩm theo slug
     */
    public function show($slug)
    {
        // 1) Lấy product + optionValues.type
        $product = Product::where('slug', $slug)
                          ->with('optionValues.type')
                          ->firstOrFail();

        // 2) Chỉ lấy những OptionType mà product này thực sự dùng
        $optionTypes = OptionType::whereHas('values.products', function($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->with(['values' => function($q) use ($product) {
                $q->whereHas('products', function($qq) use ($product) {
                    $qq->where('product_id', $product->id);
                });
            }])
            ->get();

        return view('products.show', compact('product','optionTypes'));
    }
}
