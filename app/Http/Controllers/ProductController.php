<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OptionType;
use Illuminate\Http\Request;
use App\Models\SidebarItem;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;  


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
    public function show($slug, Request $request)
{
    // 1) Lấy product kèm optionValues.type và collections → sectors
    $product = Product::with([
                        'optionValues.type',
                        'collections.sectors'
                    ])
                    ->where('slug', $slug)
                    ->firstOrFail();

    // 2) Chọn OptionType thực sự dùng cho product
    $optionTypes = OptionType::whereHas('values.products', function($q) use($product) {
            $q->where('product_id', $product->id);
        })
        ->with(['values' => function($q) use($product) {
            $q->whereHas('products', function($qq) use($product) {
                $qq->where('product_id', $product->id);
            });
        }])
        ->get();

    // 3) Xác định collection ưu tiên:
    //    nếu có ?from_collection và product thực sự thuộc collection đó → lấy nó
    //    ngược lại → fallback về collection đầu tiên
    $fromSlug = $request->query('from_collection');
    if ($fromSlug
        && $product->collections->pluck('slug')->contains($fromSlug)
    ) {
        $firstCollection = $product->collections
                                   ->firstWhere('slug', $fromSlug);
    } else {
        $firstCollection = $product->collections->first();
    }

    // 4) Lấy sector đầu tiên của collection vừa chọn (nếu có)
    $sector = $firstCollection
              ? $firstCollection->sectors->first()
              : null;

    // 5) Lấy related products: ưu tiên cùng collection, fallback random
    if ($firstCollection) {
        $relatedProducts = $firstCollection->products()
            ->where('id', '<>', $product->id)
            ->take(15)
            ->get();
    } else {
        $relatedProducts = Product::where('id', '<>', $product->id)
            ->inRandomOrder()
            ->take(15)
            ->get();
    }

    // 6) Chọn view desktop/mobile
    $agent = new Agent();
    $view  = $agent->isMobile()
             ? 'products.show-mobile'
             : 'products.show';

    // 7) Trả về view với đầy đủ dữ liệu
    return view($view, compact(
        'product',
        'optionTypes',
        'relatedProducts',
        'firstCollection',
        'sector'
    ));
}
}
