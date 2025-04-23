<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WidgetPlacement;
use App\Models\Widget;

class WidgetPlacementController extends Controller
{
    public function index()
    {
        $pl = WidgetPlacement::with('widget')->paginate(20);
        return view('admin.placements.index', compact('pl'));
    }

    public function create()
    {
        $widgets = Widget::all();
        return view('admin.placements.form', [
          'placement' => new WidgetPlacement,
          'widgets'   => $widgets,
          'action'    => route('admin.placements.store'),
          'method'    => 'POST',
        ]);
    }

    public function store(Request $r)
    {
        $r->validate([
          'region'    => 'required|string|unique:widget_placements,region',
          'widget_id' => 'required|exists:widgets,id',
        ]);
        WidgetPlacement::create($r->only(['region','widget_id']));
        return redirect()->route('admin.placements.index')
                         ->with('success','Đã gán widget.');
    }

    public function edit(WidgetPlacement $placement)
    {
        $widgets = Widget::all();
        return view('admin.placements.form', [
          'placement' => $placement,
          'widgets'   => $widgets,
          'action'    => route('admin.placements.update',$placement),
          'method'    => 'PUT',
        ]);
    }

    public function update(Request $r, WidgetPlacement $placement)
    {
        $r->validate([
          'region'    => "required|string|unique:widget_placements,region,{$placement->region},region",
          'widget_id' => 'required|exists:widgets,id',
        ]);
        $placement->update($r->only(['region','widget_id']));
        return back()->with('success','Đã cập nhật.');
    }

    public function destroy(WidgetPlacement $placement)
    {
        $placement->delete();
        return back()->with('success','Đã hủy gán.');
    }
}
