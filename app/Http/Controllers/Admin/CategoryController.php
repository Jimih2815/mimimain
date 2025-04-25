<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Helpers\SlugHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $cats        = Category::with('groups.products')
                               ->orderBy('sort_order')
                               ->get();
        $allProducts = Product::select('id','name')->orderBy('name')->get();

        return view('admin.categories.index', compact('cats','allProducts'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'name' => ['required','string','max:120', Rule::unique('categories')],
        ]);

        Category::create([
            'name'       => $r->name,
            'slug'       => SlugHelper::unique(new Category, $r->name),
            'sort_order' => (Category::max('sort_order') ?? 0) + 1,
        ]);

        return back()->with('success','Đã thêm Category!');
    }

    public function update(Request $r, Category $category)
    {
        $r->validate([
            'name'       => ['required','string','max:120',
                             Rule::unique('categories')->ignore($category->id)],
            'sort_order' => 'required|numeric',
        ]);

        $category->update([
            'name'       => $r->name,
            'slug'       => SlugHelper::unique(new Category, $r->name),
            'sort_order' => $r->sort_order,
        ]);

        return back()->with('success','Đã cập nhật Category!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success','Đã xoá Category!');
    }
}
