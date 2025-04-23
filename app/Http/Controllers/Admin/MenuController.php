<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\{MenuSection, MenuGroup, Product};

class MenuController extends Controller
{
    /* ========== VIEW ========== */
    public function index()
    {
        $sections = MenuSection::with('groups.products')
                    ->orderBy('sort_order')->get();
        $products = Product::orderBy('name')->get();

        return view('admin.menu.index', compact('sections','products'));
    }

    /* ========== SECTION CRUD ========== */
    public function storeSection(Request $r)
    {
        $r->validate(['name'=>'required']);
        MenuSection::create([
            'name'       => $r->name,
            'slug'       => Str::slug($r->name),
            'sort_order' => MenuSection::max('sort_order') + 1,
        ]);
        return back();
    }

    public function updateSection(Request $r, MenuSection $section)
    {
        $r->validate(['name'=>'required','sort_order'=>'integer']);
        $section->update([
            'name'       => $r->name,
            'slug'       => Str::slug($r->name),
            'sort_order' => $r->sort_order,
        ]);
        return back();
    }

    public function destroySection(MenuSection $section)
    {
        $section->delete(); return back();
    }

    /* ========== GROUP CRUD ========== */
    public function storeGroup(Request $r, MenuSection $section)
    {
        $r->validate(['title'=>'required']);
        $section->groups()->create([
            'title'      => $r->title,
            'sort_order' => $section->groups()->max('sort_order') + 1,
        ]);
        return back();
    }

    public function updateGroup(Request $r, MenuGroup $group)
    {
        $r->validate(['title'=>'required','sort_order'=>'integer']);
        $group->update($r->only('title','sort_order'));
        return back();
    }

    public function destroyGroup(MenuGroup $group)
    {
        $group->delete(); return back();
    }

    /* ========== PRODUCT PIVOT ========== */
    public function addProductToGroup(Request $r, MenuGroup $group)
    {
        $r->validate(['product_id'=>'required|exists:products,id']);

        if (!$group->products->contains($r->product_id)) {

            // lấy sort lớn nhất (ở pivot) rồi +1
            $next = \DB::table('menu_group_product')
                    ->where('menu_group_id', $group->id)
                    ->max('sort_order') + 1;

            $group->products()->attach($r->product_id, ['sort_order' => $next]);
        }
        return back();
    }

    public function removeProductFromGroup(MenuGroup $group, $pid)
    {
        $group->products()->detach($pid); return back();
    }
}
