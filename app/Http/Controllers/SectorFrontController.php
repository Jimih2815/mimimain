<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class SectorFrontController extends Controller
{
    /**
     * Hiển thị trang 1 sector.
     * Nếu mobile → view 'sectors.show-mobile', desktop → 'sectors.show'.
     */
    public function show($slug, Request $request)
    {
        $agent = new Agent();

        $sector = Sector::with([
                        'collections' => function($q) {
                            $q->orderBy('pivot_sort_order');
                        }
                    ])
                    ->where('slug', $slug)
                    ->firstOrFail();

        if ($agent->isMobile()) {
            return view('sectors.show-mobile', compact('sector'));
        }

        return view('sectors.show', compact('sector'));
    }
}
