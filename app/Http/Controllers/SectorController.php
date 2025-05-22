<?php

namespace App\Http\Controllers;

use App\Models\Sector;

class SectorController extends Controller
{
   public function index()
   {
       // load đúng quan hệ "collections"
       $sectors = Sector::with('collections')
           ->orderBy('sort_order')
           ->get();
       return view('sectors.index', compact('sectors'));
   }
}
