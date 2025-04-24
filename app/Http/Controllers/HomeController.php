<?php

namespace App\Http\Controllers;

use App\Models\HomePage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ, truyền banner động
     */
    public function index()
    {
        $home = HomePage::first();
        return view('home', compact('home'));
    }
}
