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
     * Hiển thị danh sách slider và tiêu đề từ HomePage
     */
    public function index()
    {
        $items = CollectionSlider::with('collection')
                  ->orderBy('sort_order')
                  ->get();

        $home  = HomePage::first();

        return view('admin.collection-sliders.index', compact('items', 'home'));
    }

    /**
     * Form thêm mới
     */
    public function create()
    {
        $collections = Collection::pluck('name','id');

        return view('admin.collection-sliders.form', compact('collections'));
    }

    /**
     * Lưu slider mới, chuyển ảnh sang WebP
     */
    public function store(Request $r)
    {
        $r->validate([
            'collection_id' => 'required|exists:collections,id',
            'image'         => 'required|image|max:4096',
            'text'          => 'required|string',
        ]);

        // Convert & upload WebP
        $path = $this->uploadAsWebp($r->file('image'), 'sliders');

        // Tính sort_order = max + 1
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
     * Form sửa
     */
    public function edit(CollectionSlider $collectionSlider)
    {
        $item        = $collectionSlider;
        $collections = Collection::pluck('name','id');

        return view('admin.collection-sliders.form', compact('item','collections'));
    }

    /**
     * Cập nhật slider, nếu có ảnh mới thì chuyển sang WebP
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
     * Xóa slider
     */
    public function destroy(CollectionSlider $collectionSlider)
    {
        $collectionSlider->delete();

        return back()->with('success', 'Đã xóa slider item.');
    }

    /**
     * Đổi vị trí up/down
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
            $a = $collectionSlider->sort_order;
            $collectionSlider->sort_order = $swap->sort_order;
            $swap->sort_order             = $a;
            $collectionSlider->save();
            $swap->save();
        }

        return back();
    }
}
