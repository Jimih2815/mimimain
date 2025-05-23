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
        // 1) Lấy collection kèm products + sectors
        $collection = Collection::with(['products', 'sectors'])
                                ->where('slug', $slug)
                                ->firstOrFail();

        // 2) Sector đầu tiên (nếu collection có nhiều sector)
        $sector = $collection->sectors->first();

        // 3) Phân mobile vs desktop
        $agent = new Agent();
        if ($agent->isMobile()) {
            $roots = SidebarItem::with('children.collection.products')
                        ->whereNull('parent_id')
                        ->orderBy('sort_order')
                        ->get();

            $favIds = auth()->check()
                ? auth()->user()->favorites()->pluck('product_id')->toArray()
                : [];

            return view('collections.show-mobile', compact(
                'collection', 'roots', 'favIds', 'sector'
            ));
        }

        return view('collections.show', compact('collection', 'sector'));
    }
}
