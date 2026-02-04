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
    // 1) Nhận diện điện thoại (không tính tablet)
    $agent = new Agent();
    $isMobile = $agent->isMobile() && ! $agent->isTablet();

    if ($isMobile) {
        // ===== Mobile: xử lý search (trước đây mobile luôn trả ALL sản phẩm => search bị “hỏng”) =====
        $q = trim((string) $request->input('q', ''));

        // Lấy sidebar + tất cả products
        $roots    = SidebarItem::with('children.collection.products')
                      ->whereNull('parent_id')
                      ->orderBy('sort_order')
                      ->get();

        // Mobile hiện tại không paginate => trả Collection.
        // - Nếu có q: filter theo tên + mô tả
        // - Nếu không có q: random như cũ
        $productQuery = Product::query();
        if ($q !== '') {
            $productQuery->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%");
            });
            // Để kết quả bớt “nhảy”, ưu tiên mới trước (bạn muốn random thì đổi lại inRandomOrder)
            $productQuery->orderByDesc('created_at');
        } else {
            $productQuery->inRandomOrder();
        }

        $products = $productQuery->get();

        // Lấy mảng ID sản phẩm user đã favorite
        $favIds = Auth::check()
        ? Auth::user()->favorites()->pluck('product_id')->toArray()
        : session('favorites', []);

        // Trả view mobile với favIds
        return view('products.index-mobile', compact('roots', 'products', 'favIds', 'q'));
    }

    // ===== Desktop: xử lý search + paginate =====
    $q = $request->input('q');
    $query = Product::with('optionValues.type');

    if ($q) {
        $query->where('name', 'like', "%{$q}%")
              ->orWhere('description', 'like', "%{$q}%");
    }

    /**
     * Random thứ tự sản phẩm theo "seed" để:
     * - Mỗi lần F5 / vào lại trang => seed mới => thứ tự mới
     * - Nhưng khi bấm trang 2,3... => giữ seed => không bị nhảy loạn/trùng sản phẩm
     */
    $seedQuery = (string) $q;
    if (!$request->has('page')) {
        session([
            'products_seed'   => random_int(1, 999999),
            'products_seed_q' => $seedQuery,
        ]);
    } elseif (session('products_seed_q') !== $seedQuery) {
        // Trường hợp user đổi q nhưng vẫn còn ?page=... (hiếm) => reset seed
        session([
            'products_seed'   => random_int(1, 999999),
            'products_seed_q' => $seedQuery,
        ]);
    }

    $seed = session('products_seed', 123456);

    // MySQL/MariaDB: RAND(seed) giúp random nhưng vẫn ổn định theo trang
    $query->orderByRaw("RAND($seed)");

    $products = $query->paginate(12)
                      ->appends(['q' => $q]);

    return view('products.index', compact('products', 'q'));
}

    /**
     * Hiển thị chi tiết sản phẩm theo slug.
     */
    public function show($slug)
    {
        // 1) Lấy product kèm optionValues.type
        $product = Product::where('slug', $slug)
                          ->with('optionValues.type')
                          ->firstOrFail();

        // 2) Lấy những OptionType thực sự dùng cho sản phẩm này
        $optionTypes = OptionType::whereHas('values.products', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->with(['values' => function ($q) use ($product) {
                $q->whereHas('products', function ($qq) use ($product) {
                    $qq->where('product_id', $product->id);
                });
            }])
            ->get();

        // 3) Lấy related products: ưu tiên cùng collection mới nhất, fallback random
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

        // 4) Phân biệt mobile vs desktop để chọn view
        $agent = new Agent();
        $view  = ($agent->isMobile() && ! $agent->isTablet())
            ? 'products.show-mobile'
            : 'products.show';

        // 5) Trả về view với đầy đủ dữ liệu
        return view($view, compact('product', 'optionTypes', 'relatedProducts'));
    }
}
