<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\{MenuSection, MenuGroup, Product, Collection};

class MenuController extends Controller
{
    /* ========== VIEW ========== */
    public function index()
    {
        $sections    = MenuSection::with('groups.products')
                         ->orderBy('sort_order')
                         ->get();
        $products    = Product::orderBy('name')->get();
        $collections = Collection::orderBy('name')->get();

        return view('admin.menu.index', compact('sections', 'products', 'collections'));
    }

    /* ========== SECTION CRUD ========== */
    public function storeSection(Request $r)
    {
        $r->validate([
            'name'          => 'required|string|max:255',
            'collection_id' => 'nullable|exists:collections,id',
        ]);

        MenuSection::create([
            'name'          => $r->name,
            'slug'          => Str::slug($r->name),
            'sort_order'    => MenuSection::max('sort_order') + 1,
            'collection_id' => $r->collection_id,
        ]);

        return back()->withInput();
    }

    public function updateSection(Request $r, MenuSection $section)
    {
        $r->validate([
            'name'          => 'required|string|max:255',
            'sort_order'    => 'nullable|integer',
            'collection_id' => 'nullable|exists:collections,id',
        ]);

        $section->update([
            'name'          => $r->name,
            'slug'          => Str::slug($r->name),
            'sort_order'    => $r->sort_order,
            'collection_id' => $r->collection_id,
        ]);

        return back()->withInput();
    }

    public function destroySection(MenuSection $section)
    {
        $section->delete();
        return back()->withInput();
    }

    /* ========== GROUP CRUD ========== */
    public function storeGroup(Request $r, MenuSection $section)
    {
        $r->validate([
            'title' => 'required|string|max:255',
        ]);

        $section->groups()->create([
            'title'      => $r->title,
            'sort_order' => $section->groups()->max('sort_order') + 1,
        ]);

        return back()->withInput();
    }

    public function updateGroup(Request $r, MenuGroup $group)
    {
        $r->validate([
            'title'      => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $group->update($r->only('title','sort_order'));

        return back()->withInput();
    }

    public function destroyGroup(MenuGroup $group)
    {
        $group->delete();
        return back()->withInput();
    }

    /* ========== PRODUCT PIVOT ========== */
    public function addProductToGroup(Request $r, MenuGroup $group)
    {
        $r->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        if (! $group->products->contains($r->product_id)) {
            $next = \DB::table('menu_group_product')
                     ->where('menu_group_id', $group->id)
                     ->max('sort_order') + 1;

            $group->products()->attach($r->product_id, [
                'sort_order' => $next
            ]);
        }

        return back()->withInput();
    }

    public function removeProductFromGroup(MenuGroup $group, $pid)
    {
        $group->products()->detach($pid);
        return back()->withInput();
    }
}
