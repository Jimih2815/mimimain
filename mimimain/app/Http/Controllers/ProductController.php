<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OptionType;         // ← import đây
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm
     */
    public function index()
    {
        // Lấy toàn bộ product kèm optionValues → type
        $products = Product::with('optionValues.type')->get();
        return view('products.index', compact('products'));
    }

    /**
     * Hiển thị chi tiết sản phẩm theo slug
     */
    public function show($slug)
    {
        $product     = Product::where('slug', $slug)
                              ->with('optionValues.type')
                              ->firstOrFail();

        $optionTypes = OptionType::with('values')->get();

        return view('products.show', compact('product', 'optionTypes'));
    }
}
