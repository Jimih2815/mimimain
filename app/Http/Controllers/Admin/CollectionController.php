<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Product;

class CollectionController extends Controller
{
    public function index()
    {
        $cols = Collection::paginate(15);
        return view('admin.collections.index', compact('cols'));
    }

    public function create()
    {
        $products = Product::all();
        return view('admin.collections.create', compact('products'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'name'=>'required',
            'slug'=>'required|unique:collections,slug',
            'products'=>'array',
        ]);
        $col = Collection::create($r->only('name','slug','description'));
        $col->products()->sync($r->products ?? []);
        return redirect()->route('admin.collections.index')
                         ->with('success','Đã tạo collection');
    }

    public function edit(Collection $collection)
    {
        $products = Product::all();
        return view('admin.collections.edit', compact('collection','products'));
    }

    public function update(Request $r, Collection $collection)
    {
        $r->validate([
            'name'=>'required',
            "slug"=>"required|unique:collections,slug,{$collection->id}",
            'products'=>'array',
        ]);
        $collection->update($r->only('name','slug','description'));
        $collection->products()->sync($r->products ?? []);
        return back()->with('success','Đã cập nhật');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();
        return back()->with('success','Đã xóa');
    }
}
