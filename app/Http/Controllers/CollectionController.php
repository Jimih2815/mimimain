<?php

namespace App\Http\Controllers;

use App\Models\Collection;
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
            // truyền luôn $collection->products vào view để xử lý dễ dàng
            return view('collections.show-mobile', compact('collection'));
        }

        // Desktop fallback
        return view('collections.show', compact('collection'));
    }
}
