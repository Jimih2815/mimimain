<?php

namespace App\Http\Controllers;

use App\Models\Sector;

class SectorController extends Controller
{
    public function index()
    {
        $sectors = Sector::with('collection')
                         ->orderBy('sort_order')
                         ->get();

        return view('sectors.index', compact('sectors'));
    }
}
