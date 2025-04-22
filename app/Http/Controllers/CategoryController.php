<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Hiển thị trang danh mục: load tất cả category + products
     */
    public function index()
    {
        // eager load products để tránh N+1
        $categories = Category::with('products')->get();
        return view('categories.index', compact('categories'));
    }
}
