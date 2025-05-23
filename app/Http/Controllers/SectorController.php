<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class SectorController extends Controller
{
    /**
     * Hiển thị danh sách sectors.
     * Nếu mobile → view 'sectors.index-mobile', desktop → 'sectors.index'.
     */
    public function index(Request $request)
    {
        $agent   = new Agent();
        $sectors = Sector::with('collections')
                         ->orderBy('sort_order')
                         ->get();

        if ($agent->isMobile()) {
            return view('sectors.index-mobile', compact('sectors'));
        }

        return view('sectors.index', compact('sectors'));
    }
}
