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
     * Display the list of favorited products (DB for auth, session for guest)
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $agent = new Agent();
        // Nếu mobile thì delegate sang indexMobile()
        if ($agent->isMobile()) {
            return $this->indexMobile($request);
        }

        // ===== NỘI DUNG CỦA index() HIỆN TẠI =====
        if (Auth::check()) {
            $ids = Auth::user()->favorites()->pluck('product_id')->toArray();
        } else {
            $ids = session('favorites', []);
        }

        $products = Product::whereIn('id', $ids)
                           ->with('optionValues.type')
                           ->get();

        return view('favorites.index', compact('products'));
    }

    /**
     * POST /favorites/toggle/{product}
     * Toggle the favorite status of a product
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Product $product)
    {
        if (Auth::check()) {
            $user = Auth::user();
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

            // Total favorites count for user
            $count = $user->favorites()->count();
        } else {
            $favorites = session('favorites', []);

            if (in_array($product->id, $favorites)) {
                // Remove from session
                $favorites = array_filter($favorites, fn($id) => $id != $product->id);
                $added = false;
            } else {
                // Add to session
                $favorites[] = $product->id;
                $added = true;
            }

            // Update session key
            session(['favorites' => array_values($favorites)]);
            // Total favorites count for guest
            $count = count($favorites);
        }

        return response()->json([
            'added' => $added,
            'count' => $count,
        ]);
    }
    public function indexMobile(Request $request)
    {
        if (Auth::check()) {
            $favIds = auth()->user()->favorites->pluck('id')->toArray();
        } else {
            $favIds = session('favorites', []);
        }

        $products = Product::with('optionValues.type')
                           ->whereIn('id', $favIds)
                           ->get();

        return view('favorites.index-mobile', compact('products','favIds'));
    }
}