<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\SidebarItem;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class CollectionController extends Controller
{
    public function show($slug, Request $request)
    {
        $collection = Collection::where('slug', $slug)
                                ->with('products')
                                ->firstOrFail();

        // Phát hiện mobile
        $agent = new Agent();
        if ($agent->isMobile()) {
            // Lấy cây menu bên mobile (nếu bạn đã dùng SidebarItem như trước)
            $roots = SidebarItem::with('children.collection.products')
                       ->whereNull('parent_id')
                       ->orderBy('sort_order')
                       ->get();

            // Thêm check auth:
            if (auth()->check()) {
                $favIds = auth()->user()->favorites()->pluck('product_id')->toArray();
            } else {
                $favIds = [];  // chưa login thì cho mảng rỗng
            }

            return view('collections.show-mobile', compact(
                'collection',
                'roots',
                'favIds'
            ));
        }

        // Desktop fallback
        return view('collections.show', compact('collection'));
    }
}
