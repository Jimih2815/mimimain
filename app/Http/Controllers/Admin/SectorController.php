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
        $sectors = Sector::with('collection')->orderBy('sort_order')->get();
        return view('admin.sectors.index', compact('sectors'));
    }

    public function create()
    {
        $collections = Collection::orderBy('name')->pluck('name','id');
        return view('admin.sectors.form', compact('collections'));
    }

   public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'image'         => 'required|image',
            'collection_id' => 'required|exists:collections,id',
            'sort_order'    => 'nullable|integer',
        ]);
        $path = $request->file('image')->store('sectors','public');
        $data['image'] = $path;

        Sector::create($data);

        // Chuyển về lại trang admin/sectors
        return redirect()
            ->route('admin.sectors.index')
            ->with('success', 'Thêm ngành hàng thành công.');
    }

    public function edit(Sector $sector)
    {
        $collections = Collection::orderBy('name')->pluck('name','id');
        return view('admin.sectors.form', compact('sector','collections'));
    }

    public function update(Request $request, Sector $sector)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'image'         => 'nullable|image',
            'collection_id' => 'required|exists:collections,id',
            'sort_order'    => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($sector->image);
            $data['image'] = $request->file('image')->store('sectors','public');
        }

        $sector->update($data);

        // Chuyển về lại trang admin/sectors
        return redirect()
            ->route('admin.sectors.index')
            ->with('success', 'Cập nhật ngành hàng thành công.');
    }

    public function destroy(Sector $sector)
    {
        Storage::disk('public')->delete($sector->image);
        $sector->delete();

        // Nếu muốn, cũng có thể ép redirect về index
        return redirect()
            ->route('admin.sectors.index')
            ->with('success', 'Xóa ngành hàng thành công.');
    }
}
