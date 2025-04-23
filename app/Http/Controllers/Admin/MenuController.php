<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuSection;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $sections = MenuSection::with('items')->orderBy('sort_order')->get();
        return view('admin.menu.index', compact('sections'));
    }

    /* --------- SECTION CRUD --------- */
    public function storeSection(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:menu_sections,slug',
        ]);
        $data['sort_order'] = MenuSection::max('sort_order') + 1;
        MenuSection::create($data);
        return back()->with('success','Đã tạo Section mới');
    }

    public function updateSection(Request $r, MenuSection $section)
    {
        $r->validate(['name'=>'required','slug'=>"required|unique:menu_sections,slug,{$section->id}"]);
        $section->update($r->only('name','slug','sort_order'));
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
        $data = $r->validate([
            'label'=>'required|string',
            'url'  =>'required|string',
        ]);
        $data['sort_order'] = $section->items()->max('sort_order') + 1;
        $section->items()->create($data);
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
