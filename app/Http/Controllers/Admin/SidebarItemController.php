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
        SidebarItem::create($r->validate([
            'name'          => 'required|string',
            'parent_id'     => 'nullable|exists:sidebar_items,id',
            'collection_id' => 'nullable|exists:collections,id',
            'sort_order'    => 'integer',
        ]));
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
        $sidebarItem->update($r->validate([
            'name'          => 'required|string',
            'parent_id'     => 'nullable|exists:sidebar_items,id',
            'collection_id' => 'nullable|exists:collections,id',
            'sort_order'    => 'integer',
        ]));
        return redirect()->route('admin.sidebar-items.index');
    }

    public function destroy(SidebarItem $sidebarItem)
    {
        $sidebarItem->delete();
        return back();
    }
}
