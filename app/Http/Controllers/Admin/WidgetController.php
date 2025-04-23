<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Widget;
use App\Models\Collection;

class WidgetController extends Controller
{
    public function index()
    {
        $widgets = Widget::orderBy('slug')->paginate(20);
        return view('admin.widgets.index', compact('widgets'));
    }

    public function create()
    {
        $cols = Collection::all();
        return view('admin.widgets.form', [
            'widget' => new Widget,
            'collections'=>$cols,
            'action'=>route('admin.widgets.store'),
            'method'=>'POST'
        ]);
    }

    public function store(Request $r)
    {
        $r->validate([
            'name'=>'required',
            'slug'=>'required|unique:widgets,slug',
            'type'=>'required|in:banner,button,html',
            'collection_id'=>'nullable|exists:collections,id',
            'image'=>'nullable|string',
            'button_text'=>'nullable|string',
            'html'=>'nullable|string',
        ]);
        Widget::create($r->all());
        return redirect()->route('admin.widgets.index')
                         ->with('success','Đã tạo widget');
    }

    public function edit(Widget $widget)
    {
        $cols = Collection::all();
        return view('admin.widgets.form', [
            'widget'=>$widget,
            'collections'=>$cols,
            'action'=>route('admin.widgets.update',$widget),
            'method'=>'PUT'
        ]);
    }

    public function update(Request $r, Widget $widget)
    {
        $r->validate([
            'name'=>'required',
            "slug"=>"required|unique:widgets,slug,{$widget->id}",
            'type'=>'required|in:banner,button,html',
            'collection_id'=>'nullable|exists:collections,id',
            'image'=>'nullable|string',
            'button_text'=>'nullable|string',
            'html'=>'nullable|string',
        ]);
        $widget->update($r->all());
        return back()->with('success','Đã cập nhật widget');
    }

    public function destroy(Widget $widget)
    {
        $widget->delete();
        return back()->with('success','Đã xóa widget');
    }
}
