<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryGroup;
use Illuminate\Http\Request;

class CategoryGroupController extends Controller
{
    /* ========== CRUD Group ========== */

    public function store(Category $category, Request $r)
    {
        $r->validate(['title' => 'required|string|max:120']);

        $category->groups()->create([
            'title'      => $r->title,
            'sort_order' => ($category->groups()->max('sort_order') ?? 0) + 1,
        ]);

        return back()->with('success','Đã thêm mục dropdown!');
    }

    public function update(CategoryGroup $group, Request $r)
    {
        $r->validate([
            'title'      => 'required|string|max:120',
            'sort_order' => 'numeric',
        ]);

        $group->update($r->only('title','sort_order'));
        return back()->with('success','Đã cập nhật mục!');
    }

    public function destroy(CategoryGroup $group)
    {
        $group->delete();
        return back()->with('success','Đã xoá mục!');
    }

    /* ========== Products in Group ========== */

    public function attachProduct(CategoryGroup $group, Request $r)
    {
        $r->validate(['pid' => 'required|exists:products,id']);
    
        /* 1) Bảo đảm group đã có menu_group_id -------------------------- */
        if (is_null($group->menu_group_id)) {
            \DB::transaction(function () use ($group) {
    
                // a. Tạo (hoặc lấy) section khớp với category
                $section = \App\Models\MenuSection::firstOrCreate(
                    ['slug' => $group->category->slug],
                    ['name' => $group->category->name,
                     'sort_order' => $group->category->sort_order ?? 0]
                );
    
                // b. Tạo item cho mega-menu và lưu khoá lại group
                $item = \App\Models\MenuItem::create([
                    'menu_section_id' => $section->id,
                    'label'           => $group->title,
                    'url'             => '#',
                    'sort_order'      => $group->sort_order,
                ]);
    
                $group->updateQuietly(['menu_group_id' => $item->id]);
            });
        }
    
        /* 2) Pivot cho trang admin ------------------------------------- */
        $max = $group->products()->max('category_group_product.sort_order') ?? 0;
        $group->products()->syncWithoutDetaching([
            $r->pid => ['sort_order' => $max + 1],
        ]);
    
        /* 3) Pivot cho mega-menu header -------------------------------- */
        \DB::table('menu_group_product')->updateOrInsert(
            ['menu_group_id' => $group->menu_group_id, 'product_id' => $r->pid],
            ['sort_order'    => $max + 1]
        );
    
        return back()->with('success', 'Đã thêm sản phẩm!');
    }

public function detachProduct(CategoryGroup $group, $pid)
{
    $group->products()->detach($pid);

    \DB::table('menu_group_product')
        ->where('menu_group_id',$group->menu_group_id)
        ->where('product_id',$pid)
        ->delete();

    return back()->with('success','Đã xoá sản phẩm!');
}
}
