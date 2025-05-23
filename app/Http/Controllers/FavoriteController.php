<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class FavoriteController extends Controller
{
    /**
     * GET /favorites
     * Hiển thị danh sách sản phẩm yêu thích
     */
    public function index(Request $request)
    {
        $agent = new Agent();
        if ($agent->isMobile()) {
            return $this->indexMobile($request);
        }

        // với desktop
        if (Auth::check()) {
            $ids = Auth::user()
                       ->favorites()
                       ->pluck('product_id')
                       ->map(fn($id) => (int)$id)
                       ->toArray();
        } else {
            $ids = collect(session('favorites', []))
                   ->map(fn($id) => (int)$id)
                   ->toArray();
        }

        $products = Product::whereIn('id', $ids)
                           ->with('optionValues.type')
                           ->get();

        return view('favorites.index', compact('products'));
    }

    /**
     * POST /favorites/toggle/{product}
     * Toggle trạng thái yêu thích của một sản phẩm
     */
    public function toggle(Product $product)
    {
        if (Auth::check()) {
            $user   = Auth::user();
            $exists = $user->favorites()
                           ->where('product_id', $product->id)
                           ->exists();

            if ($exists) {
                $user->favorites()->detach($product->id);
                $added = false;
            } else {
                $user->favorites()->attach($product->id);
                $added = true;
            }

            $count = $user->favorites()->count();
        } else {
            // guest lưu vào session
            $favorites = session('favorites', []);

            if (in_array($product->id, $favorites)) {
                $favorites = array_filter($favorites, fn($id) => $id != $product->id);
                $added = false;
            } else {
                $favorites[] = $product->id;
                $added = true;
            }

            session(['favorites' => array_values($favorites)]);
            $count = count($favorites);
        }

        return response()->json([
            'added'       => $added,
            'total_items' => $count,
        ]);
    }

    /**
     * GET /favorites (mobile)
     */
    public function indexMobile(Request $request)
    {
        if (Auth::check()) {
            $favIds = Auth::user()
                          ->favorites()
                          ->pluck('product_id')
                          ->map(fn($id) => (int)$id)
                          ->toArray();
        } else {
            $favIds = collect(session('favorites', []))
                          ->map(fn($id) => (int)$id)
                          ->toArray();
        }

        $products = Product::with('optionValues.type')
                           ->whereIn('id', $favIds)
                           ->get();

        return view('favorites.index-mobile', compact('products', 'favIds'));
    }
}
