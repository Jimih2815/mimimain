<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * GET /favorites
     * Display the list of favorited products (DB for auth, session for guest)
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (Auth::check()) {
            // Logged-in user: fetch favorites from database
            $ids = Auth::user()
                      ->favorites()
                      ->pluck('product_id')
                      ->toArray();
        } else {
            // Guest user: fetch favorites from session
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
}