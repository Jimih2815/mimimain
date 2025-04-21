<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        // Lấy classification (màu/variant) theo id
        $classification = Classification::with([
            'product',               // quan hệ product
            'product.classifications', // lấy hết các variant để làm sub‑images
            'options'                // lấy các option (size) của variant này
        ])->findOrFail($id);

        $product = $classification->product;

        // Sub‑images = img của mỗi variant
        $subImages = $product->classifications->pluck('img')->filter()->toArray();

        // Main image = img của chính classification này
        $mainImage = $classification->img;

        // Color variants (ô chọn màu)
        $colorVariants = $product->classifications;

        // Size options
        $sizeOptions = $classification->options;

        return view('products.show', compact(
            'product','classification','subImages','mainImage','colorVariants','sizeOptions'
          ));
    }
}
