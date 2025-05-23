<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\SidebarItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class CollectionController extends Controller
{
    /**
     * Hiển thị trang Collection, phân mobile vs desktop
     */
    public function show($slug, Request $request)
    {
        // 1) Lấy collection kèm products + sectors
        $collection = Collection::with(['products', 'sectors'])
                                ->where('slug', $slug)
                                ->firstOrFail();

        // 2) Sector đầu tiên (nếu collection có nhiều sector)
        $sector = $collection->sectors->first();

        // 3) Phân mobile vs desktop
        $agent = new Agent();
        if ($agent->isMobile()) {
            // 3.1) Menu cha/con
            $roots = SidebarItem::with('children.collection.products')
                        ->whereNull('parent_id')
                        ->orderBy('sort_order')
                        ->get();

            // 3.2) ID sản phẩm đã yêu thích
            if (Auth::check()) {
                $favIds = Auth::user()
                              ->favorites()                     // relation belongsToMany(Product)
                              ->pluck('product_id')             // lấy product_id
                              ->map(fn($id) => (int)$id)
                              ->toArray();
            } else {
                // guest: lấy từ session('favorites'), default []
                $favIds = collect(session('favorites', []))
                              ->map(fn($id) => (int)$id)
                              ->toArray();
            }

            return view('collections.show-mobile', compact(
                'collection', 'roots', 'favIds', 'sector'
            ));
        }

        // Desktop
        return view('collections.show', compact('collection', 'sector'));
    }
}
