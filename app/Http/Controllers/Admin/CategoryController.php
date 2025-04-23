<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $cats = Category::orderBy('sort_order')->get();   // trả $cats
        return view('admin.categories.index', compact('cats'));
    }

    public function store(Request $r)
    {
        $r->validate(['name'=>'required|string']);
        Category::create([
            'name'       => $r->name,
            'sort_order' => (Category::max('sort_order') ?? 0) + 1,
        ]);
        return back()->with('success','Đã thêm Category!');
    }

    public function update(Request $r, Category $category)
    {
        $r->validate([
            'name'       => 'required|string',
            'sort_order' => 'required|numeric',
        ]);
        $category->update($r->only('name','sort_order'));
        return back()->with('success','Đã cập nhật Category!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success','Đã xoá Category!');
    }
}
