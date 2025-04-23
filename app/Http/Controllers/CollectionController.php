<?php

namespace App\Http\Controllers;

use App\Models\Collection;

class CollectionController extends Controller
{
    public function show($slug)
    {
        $collection = Collection::where('slug',$slug)
                                ->with('products')
                                ->firstOrFail();
        return view('collections.show', compact('collection'));
    }
}
