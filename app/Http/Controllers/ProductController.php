<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OptionType;
use Illuminate\Http\Request;
use App\Models\SidebarItem;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm và search
     */
   
    public function index(Request $request)
    {
        // 1) Nhận diện mobile (simple User-Agent check)
        $isMobile = preg_match('/Mobile|Android|iPhone/', $request->header('User-Agent'));

        if ($isMobile) {
            // Lấy sidebar + tất cả products
            $roots    = SidebarItem::with('children.collection.products')
                          ->whereNull('parent_id')
                          ->orderBy('sort_order')
                          ->get();
            $products = Product::all();

            // Lấy mảng ID sản phẩm user đã favorite
            $favIds = Auth::check()
            ? Auth::user()->favorites()->pluck('product_id')->toArray()
            : [];

            // Trả view mobile với favIds
            return view('products.index-mobile', compact('roots', 'products', 'favIds'));
        }

        // ===== Desktop: xử lý search + paginate =====
        $q = $request->input('q');
        $query = Product::with('optionValues.type');

        if ($q) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
        }

        $products = $query->paginate(12)
                          ->appends(['q' => $q]);

        return view('products.index', compact('products', 'q'));
    }

    /**
     * Hiển thị chi tiết sản phẩm theo slug.
     */
    public function show($slug)
    {
        // 1) Lấy product với optionValues.type
        $product = Product::where('slug', $slug)
                          ->with('optionValues.type')
                          ->firstOrFail();

        // 2) Lấy các OptionType thực sự dùng cho product này
        $optionTypes = OptionType::whereHas('values.products', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->with(['values' => function ($q) use ($product) {
                $q->whereHas('products', function ($qq) use ($product) {
                    $qq->where('product_id', $product->id);
                });
            }])
            ->get();

        // 3) Related products: ưu tiên cùng collection mới nhất, fallback random
        $latestCollection = $product->collections()
                                    ->orderBy('created_at', 'desc')
                                    ->first();

        if ($latestCollection) {
            $relatedProducts = $latestCollection->products()
                ->where('id', '<>', $product->id)
                ->take(15)
                ->get();
        } else {
            $relatedProducts = Product::where('id', '<>', $product->id)
                ->inRandomOrder()
                ->take(15)
                ->get();
        }

        return view('products.show', compact('product', 'optionTypes', 'relatedProducts'));
    }
}
