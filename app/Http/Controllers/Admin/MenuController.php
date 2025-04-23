<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuSection;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use App\Models\Product; 
use Illuminate\Support\Str; 
class MenuController extends Controller
{
    public function index()
{
    $sections = MenuSection::with('items')->orderBy('sort_order')->get();
    $products = Product::orderBy('name')->get();        // 🔑
    return view('admin.menu.index', compact('sections','products'));
}

    /* --------- SECTION CRUD --------- */
    public function storeSection(Request $r)
{
    $r->validate(['name'=>'required|string']);
    MenuSection::create([
        'name'       => $r->name,
        'slug'       => Str::slug($r->name),   // 🔑 auto slug
        'sort_order' => MenuSection::max('sort_order') + 1,
    ]);
    return back()->with('success','Đã tạo Section');
}

public function updateSection(Request $r, MenuSection $section)
{
    $r->validate(['name'=>'required']);
    $section->update([
        'name'       => $r->name,
        'slug'       => Str::slug($r->name),   // 🔑 regenerate slug
        'sort_order' => $r->sort_order,
    ]);
    return back()->with('success','Đã cập nhật Section');
}

    public function destroySection(MenuSection $section)
    {
        $section->delete();
        return back()->with('success','Đã xoá Section');
    }

    /* --------- ITEM CRUD --------- */
    public function storeItem(Request $r, MenuSection $section)
{
    $r->validate([
        'label' => 'required',
        'url'   => 'required',
    ]);
    $section->items()->create([
        'label'      => $r->label,
        'url'        => $r->url,
        'sort_order' => $section->items()->max('sort_order') + 1,
    ]);
    return back()->with('success','Đã thêm Item');
}

    public function updateItem(Request $r, MenuItem $item)
    {
        $r->validate(['label'=>'required','url'=>'required']);
        $item->update($r->only('label','url','sort_order'));
        return back()->with('success','Đã cập nhật Item');
    }

    public function destroyItem(MenuItem $item)
    {
        $item->delete();
        return back()->with('success','Đã xoá Item');
    }
}
