<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SidebarItem;
use App\Models\Collection;
use Illuminate\Http\Request;

class SidebarItemController extends Controller
{
    public function index()
    {
        $items = SidebarItem::with('children','collection')->whereNull('parent_id')->orderBy('sort_order')->get();
        return view('admin.sidebar-items.index', compact('items'));
    }

    public function create()
    {
        $parents    = SidebarItem::whereNull('parent_id')->pluck('name','id');
        $collections= Collection::pluck('name','id');
        return view('admin.sidebar-items.create', compact('parents','collections'));
    }

    public function store(Request $r)
    {
        // Validate cha + array children
        $data = $r->validate([
        'name'          => 'required|string',
        'sort_order'    => 'integer',
        'children'      => 'nullable|array',
        'children.*.name'          => 'required_with:children|string',
        'children.*.collection_id' => 'required_with:children|exists:collections,id',
        ]);

        // Tạo mục cha
        $parent = SidebarItem::create([
        'name'       => $data['name'],
        'sort_order' => $data['sort_order'] ?? 0,
        'parent_id'  => null,
        'collection_id' => null,
        ]);

        // Tạo từng mục con, nếu có
        if (!empty($data['children'])) {
        foreach ($data['children'] as $idx => $child) {
            SidebarItem::create([
            'name'          => $child['name'],
            'collection_id'=> $child['collection_id'],
            'parent_id'    => $parent->id,
            'sort_order'   => $idx,      // hoặc dùng một trường sort_order riêng nếu cần
            ]);
        }
        }

        return redirect()->route('admin.sidebar-items.index');
    }

    public function edit(SidebarItem $sidebarItem)
    {
        $parents    = SidebarItem::whereNull('parent_id')->where('id','!=',$sidebarItem->id)->pluck('name','id');
        $collections= Collection::pluck('name','id');
        return view('admin.sidebar-items.edit', compact('sidebarItem','parents','collections'));
    }

    public function update(Request $r, SidebarItem $sidebarItem)
{
    // 1. Validate input parent + children
    $data = $r->validate([
        'name'                  => 'required|string',
        'sort_order'            => 'integer',
        'children'              => 'nullable|array',
        'children.*.id'         => 'nullable|integer|exists:sidebar_items,id',
        'children.*.name'       => 'required_with:children|string',
        'children.*.collection_id' => 'required_with:children|exists:collections,id',
    ]);

    // 2. Cập nhật mục cha
    $sidebarItem->update([
        'name'       => $data['name'],
        'sort_order' => $data['sort_order'] ?? 0,
    ]);

    // 3. Xử lý mục con
    $existingIds = $sidebarItem->children()->pluck('id')->toArray();
    $submitted  = [];
    
    if (!empty($data['children'])) {
        foreach ($data['children'] as $index => $childData) {
            if (!empty($childData['id']) && in_array($childData['id'], $existingIds)) {
                // a) Update mục con cũ
                $child = SidebarItem::find($childData['id']);
                $child->update([
                    'name'          => $childData['name'],
                    'collection_id' => $childData['collection_id'],
                    'sort_order'    => $index,
                ]);
                $submitted[] = $childData['id'];
            } else {
                // b) Tạo mới mục con
                $newChild = SidebarItem::create([
                    'name'          => $childData['name'],
                    'collection_id' => $childData['collection_id'],
                    'parent_id'     => $sidebarItem->id,
                    'sort_order'    => $index,
                ]);
                $submitted[] = $newChild->id;
            }
        }
    }

    // 4. Xóa các mục con đã bị remove (có trong DB nhưng không có trong request)
    $toDelete = array_diff($existingIds, $submitted);
    if (!empty($toDelete)) {
        SidebarItem::whereIn('id', $toDelete)->delete();
    }

    return redirect()->route('admin.sidebar-items.index')
                     ->with('success','Cập nhật sidebar thành công');
}

    public function destroy(SidebarItem $sidebarItem)
    {
        $sidebarItem->delete();
        return back();
    }
}
