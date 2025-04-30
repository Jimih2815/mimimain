<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HandlesWebpUpload;
use Illuminate\Http\Request;
use App\Models\CollectionSlider;
use App\Models\Collection;
use App\Models\HomePage;

class CollectionSliderController extends Controller
{
    use HandlesWebpUpload;
    
    /**
     * INDEX: hiển thị danh sách slider và tiêu đề từ HomePage
     */
    public function index()
    {
        $items = CollectionSlider::with('collection')
                  ->orderBy('sort_order')
                  ->get();

        $home = HomePage::first();

        return view('admin.collection-sliders.index', compact('items', 'home'));
    }

    /**
     * CREATE: form thêm mới
     */
    public function create()
    {
        $collections = Collection::pluck('name', 'id');
        return view('admin.collection-sliders.form', compact('collections'));
    }

    /**
     * STORE: lưu slider mới, convert ảnh sang WebP
     */
    public function store(Request $r)
    {
        $r->validate([
            'collection_id' => 'required|exists:collections,id',
            'image'         => 'required|image|max:4096',
            'text'          => 'required|string',
        ]);

        // Upload WebP
        $path = $this->uploadAsWebp($r->file('image'), 'sliders');

        // Tự động tính sort_order = max + 1
        $order = CollectionSlider::max('sort_order') + 1;

        CollectionSlider::create([
            'collection_id' => $r->collection_id,
            'image'         => $path,
            'text'          => $r->text,
            'sort_order'    => $order,
        ]);

        return redirect()
               ->route('admin.collection-sliders.index')
               ->with('success', 'Đã thêm slider item.');
    }

    /**
     * EDIT: form sửa
     */
    public function edit(CollectionSlider $collectionSlider)
    {
        $item        = $collectionSlider;
        $collections = Collection::pluck('name', 'id');
        return view('admin.collection-sliders.form', compact('item', 'collections'));
    }

    /**
     * UPDATE: cập nhật slider, đổi ảnh nếu có
     */
    public function update(Request $r, CollectionSlider $collectionSlider)
    {
        $r->validate([
            'collection_id' => 'required|exists:collections,id',
            'image'         => 'nullable|image|max:4096',
            'text'          => 'required|string',
        ]);

        $data = [
            'collection_id' => $r->collection_id,
            'text'          => $r->text,
        ];

        if ($r->hasFile('image')) {
            $data['image'] = $this->uploadAsWebp($r->file('image'), 'sliders');
        }

        $collectionSlider->update($data);

        return back()->with('success', 'Đã cập nhật slider item.');
    }

    /**
     * DESTROY: xóa slider
     */
    public function destroy(CollectionSlider $collectionSlider)
    {
        $collectionSlider->delete();
        return back()->with('success', 'Đã xóa slider item.');
    }

    /**
     * MOVE: đổi vị trí lên/xuống (giũ nguyên logic của bạn)
     */
    public function move(Request $r, CollectionSlider $collectionSlider)
    {
        $dir = $r->input('dir');
        $swap = CollectionSlider::where('sort_order', 
                    $dir === 'up' ? '<' : '>', 
                    $collectionSlider->sort_order)
                ->orderBy('sort_order', $dir === 'up' ? 'desc' : 'asc')
                ->first();

        if ($swap) {
            $tmp = $collectionSlider->sort_order;
            $collectionSlider->sort_order = $swap->sort_order;
            $swap->sort_order             = $tmp;
            $collectionSlider->save();
            $swap->save();
        }

        return back();
    }

    /**
     * REORDER: nhận mảng IDs và cập nhật sort_order qua AJAX
     */
    public function reorder(Request $request)
    {
        $ids = $request->input('ids', []);
        foreach ($ids as $index => $id) {
            CollectionSlider::where('id', $id)
                ->update(['sort_order' => $index + 1]);
        }
        return response()->json(['status' => 'success']);
    }
}
