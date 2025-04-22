<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // 1) Hiển thị danh mục gốc
    public function index()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('categories.index', compact('categories'));
    }

    // 2) Nếu có children → show danh mục con, else → show Products
    public function show($id)
    {
        $category = Category::with('children')->findOrFail($id);

        if ($category->children->isNotEmpty()) {
            $children = $category->children;
            return view('categories.children', compact('category','children'));
        }

        // leaf category → show Products
        $products = Product::where('category_id', $id)->get();
        return view('categories.products', compact('category','products'));
    }
}
