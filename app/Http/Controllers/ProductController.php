<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Classification;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * (3) Index: show toàn bộ variants (classification) dưới dạng card
     */
    public function index()
    {
        $items = Classification::with(['product','options'])->get();
        return view('products.index', compact('items'));
    }

    /**
     * (4) Product Detail: chọn màu (variant)
     */
    public function showProduct($id)
    {
        $product = Product::with('classifications')->findOrFail($id);
        return view('products.detail', compact('product'));
    }

    /**
     * (5) Classification Detail: chọn size
     */
    public function show($id)
    {
        $classification = Classification::with([
            'product',
            'product.classifications',
            'options'
        ])->findOrFail($id);

        $product       = $classification->product;
        $subImages     = $product->classifications->pluck('img')->filter()->toArray();
        $mainImage     = $classification->img;
        $colorVariants = $product->classifications;
        $sizeOptions   = $classification->options;

        return view('products.show', compact(
            'product','classification','subImages','mainImage','colorVariants','sizeOptions'
        ));
    }
}
