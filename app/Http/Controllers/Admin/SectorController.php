<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HandlesWebpUpload;
use App\Models\Sector;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SectorController extends Controller
{
    use HandlesWebpUpload; 

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
            'name'                        => 'required|string|max:255',
            'slug'                        => 'required|string|max:255|unique:sectors,slug',
            'image'                       => 'required|image|max:4096',
            'sort_order'                  => 'nullable|integer',
            'collections'                 => 'nullable|array',
            'collections.*.collection_id' => 'required|integer|exists:collections,id',
            'collections.*.custom_name'   => 'nullable|string|max:255',
            'collections.*.custom_image'  => 'nullable|image',
            'collections.*.sort_order'    => 'nullable|integer',
        ]);

        // Upload & convert ảnh chính sang WebP
       $data['image'] = $this->uploadAsWebp(
           $request->file('image'),
           'sectors'
       );

        // Tạo sector
        $sector = Sector::create([
            'name'       => $data['name'],
            'slug'       => $data['slug'],
            'image'      => $data['image'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        // Build mảng sync pivot
        $syncData = [];
        if (!empty($data['collections'])) {
            foreach ($data['collections'] as $idx => $info) {
                if (empty($info['collection_id'])) {
                    continue;
                }
                $pivot = [
                    'custom_name' => $info['custom_name'] ?? null,
                    'sort_order'  => $info['sort_order']  ?? 0,
                ];
                   // upload & convert ảnh tuỳ chỉnh nếu có
               if ($request->hasFile("collections.{$idx}.custom_image")) {
                   $pivot['custom_image'] = $this->uploadAsWebp(
                       $request->file("collections.{$idx}.custom_image"),
                       'sector_collections'
                   );
               }
                $syncData[ $info['collection_id'] ] = $pivot;
            }
        }

        $sector->collections()->sync($syncData);

        return redirect()
               ->route('admin.sectors.index')
               ->with('success', 'Tạo sector thành công.');
    }

    public function edit(Sector $sector)
    {
        $collections = Collection::orderBy('name')->pluck('name','id');
        return view('admin.sectors.form', compact('sector','collections'))
               ->with('isEdit', true);
    }

    public function update(Request $request, Sector $sector)
    {
        $data = $request->validate([
            'name'                        => 'required|string|max:255',
            'slug'                        => "required|string|max:255|unique:sectors,slug,{$sector->id}",
            'image'                       => 'nullable|image|max:4096',
            'sort_order'                  => 'nullable|integer',
            'collections'                 => 'nullable|array',
            'collections.*.collection_id' => 'required|integer|exists:collections,id',
            'collections.*.custom_name'   => 'nullable|string|max:255',
            'collections.*.custom_image'  => 'nullable|image',
            'collections.*.sort_order'    => 'nullable|integer',
        ]);

        // Cập nhật ảnh chính nếu upload mới
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($sector->image);
            $data['image'] = $this->uploadAsWebp(
               $request->file('image'),
               'sectors'
           );
        }

        // Update sector
        $sector->update([
            'name'       => $data['name'],
            'slug'       => $data['slug'],
            'image'      => $data['image'] ?? $sector->image,
            'sort_order' => $data['sort_order'] ?? $sector->sort_order,
        ]);

        // Build mảng sync pivot
        $syncData = [];
        if (!empty($data['collections'])) {
            foreach ($data['collections'] as $idx => $info) {
                if (empty($info['collection_id'])) {
                    continue;
                }
                $pivot = [
                    'custom_name' => $info['custom_name'] ?? null,
                    'sort_order'  => $info['sort_order']  ?? 0,
                ];
                // upload ảnh tuỳ chỉnh mới, xóa ảnh cũ nếu có
                if ($request->hasFile("collections.{$idx}.custom_image")) {
                    $old = $sector->collections()
                        ->wherePivot('collection_id', $info['collection_id'])
                        ->first()?->pivot->custom_image;
                    if ($old) {
                        Storage::disk('public')->delete($old);
                    }
                     $pivot['custom_image'] = $this->uploadAsWebp(
                       $request->file("collections.{$idx}.custom_image"),
                       'sector_collections'
                   );
                }
                $syncData[ $info['collection_id'] ] = $pivot;
            }
        }

        $sector->collections()->sync($syncData);

        return redirect()
               ->route('admin.sectors.index')
               ->with('success', 'Cập nhật sector thành công.');
    }

    public function destroy(Sector $sector)
    {
        // Xóa ảnh chính
        Storage::disk('public')->delete($sector->image);

        // Xóa ảnh pivot
        foreach ($sector->collections as $col) {
            if ($col->pivot->custom_image) {
                Storage::disk('public')->delete($col->pivot->custom_image);
            }
        }

        $sector->delete();

        return redirect()
               ->route('admin.sectors.index')
               ->with('success', 'Xóa sector thành công.');
    }
    public function reorder(Request $request)
{
    foreach ($request->input('order') as $item) {
        \App\Models\Sector::where('id', $item['id'])
            ->update(['sort_order' => $item['sort_order']]);
    }

    return response()->json(['success' => true]);
}

}
