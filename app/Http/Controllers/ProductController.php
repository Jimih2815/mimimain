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

        $query = Product::with('optionValues.type');

        if ($q) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
        }

        $products = $query->get();

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
