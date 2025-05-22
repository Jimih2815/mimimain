<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sector;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SectorController extends Controller
{
    public function index()
    {
        $sectors = Sector::with('collections')->orderBy('sort_order')->get();
        return view('admin.sectors.index', compact('sectors'));
    }

public function create()
{
    $sector      = new Sector();
    $collections = Collection::orderBy('name')->pluck('name','id');
    return view('admin.sectors.form', compact('sector','collections'))
           ->with('isEdit', false);
}


    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:sectors,slug',
            'image'       => 'required|image',
            'sort_order'  => 'nullable|integer',
            'collections' => 'nullable|array', // mảng [id => [active, custom_name, custom_image, sort_order]]
        ]);

        // Upload sector image
        $data['image'] = $request->file('image')->store('sectors','public');

        // Tạo sector
        $sector = Sector::create($data);

        // Xử lý pivot
        if ($request->has('collections')) {
            $sync = [];
            foreach ($request->input('collections') as $colId => $info) {
                if (empty($info['active'])) {
                    continue; // không chọn
                }
                $pivot = [
                    'custom_name'  => $info['custom_name'] ?? null,
                    'sort_order'   => $info['sort_order'] ?? 0,
                ];
                // upload hình tuỳ chỉnh
                if ($request->hasFile("collections.{$colId}.custom_image")) {
                    $pivot['custom_image'] = 
                        $request->file("collections.{$colId}.custom_image")
                                ->store('sector_collections','public');
                }
                $sync[$colId] = $pivot;
            }
            $sector->collections()->sync($sync);
        }

        return redirect()
            ->route('admin.sectors.index')
            ->with('success', 'Tạo sector thành công.');
    }

    public function edit(Sector $sector)
    {
        $collections = Collection::orderBy('name')->pluck('name','id');
        // Lấy id các collection đã gắn
        $selected = $sector->collections->pluck('pivot.collection_id')->toArray();
        return view('admin.sectors.form', compact('sector','collections','selected'));
    }

    public function update(Request $request, Sector $sector)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => "required|string|max:255|unique:sectors,slug,{$sector->id}",
            'image'       => 'nullable|image',
            'sort_order'  => 'nullable|integer',
            'collections' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($sector->image);
            $data['image'] = $request->file('image')->store('sectors','public');
        }

        $sector->update($data);

        // Sync pivot
        if ($request->has('collections')) {
            $sync = [];
            foreach ($request->input('collections') as $colId => $info) {
                if (empty($info['active'])) {
                    continue;
                }
                $pivot = [
                    'custom_name' => $info['custom_name'] ?? null,
                    'sort_order'  => $info['sort_order'] ?? 0,
                ];
                if ($request->hasFile("collections.{$colId}.custom_image")) {
                    // xóa hình cũ nếu có
                    Storage::disk('public')
                        ->delete($sector->collections()->where('collection_id',$colId)->first()->pivot->custom_image);
                    $pivot['custom_image'] = 
                        $request->file("collections.{$colId}.custom_image")
                                ->store('sector_collections','public');
                }
                $sync[$colId] = $pivot;
            }
            $sector->collections()->sync($sync);
        }

        return redirect()
            ->route('admin.sectors.index')
            ->with('success', 'Cập nhật sector thành công.');
    }

    public function destroy(Sector $sector)
    {
        Storage::disk('public')->delete($sector->image);
        // xóa ảnh pivot nếu cần
        foreach ($sector->collections as $col) {
            Storage::disk('public')->delete($col->pivot->custom_image);
        }
        $sector->delete();

        return redirect()
            ->route('admin.sectors.index')
            ->with('success', 'Xóa sector thành công.');
    }
}
