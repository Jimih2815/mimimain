<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;

class SectorFrontController extends Controller
{
    public function show($slug)
    {
        $sector = Sector::with(['collections' => function($q){
            $q->orderBy('pivot_sort_order');
        }])->where('slug',$slug)->firstOrFail();

        return view('sectors.show', compact('sector'));
    }
}
