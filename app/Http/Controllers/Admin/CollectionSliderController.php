<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CollectionSlider;
use App\Models\Collection;
use App\Models\HomePage;


class CollectionSliderController extends Controller
{
    public function index()
    {
      $items = CollectionSlider::with('collection')
        ->orderBy('sort_order')
        ->get();
      $home  = HomePage::first();                   
      return view('admin.collection-sliders.index', compact('items','home'));
    }

    public function create()
    {
        $collections = Collection::pluck('name','id');
        return view('admin.collection-sliders.form', compact('collections'));
    }

    public function store(Request $r)
    {
        $r->validate([
          'collection_id' => 'required|exists:collections,id',
          'image'         => 'required|image|max:4096',
          'text'          => 'required|string',
        ]);

        $path = $r->file('image')->store('sliders','public');
        // tính sort_order = max+1
        $order = CollectionSlider::max('sort_order') + 1;

        CollectionSlider::create([
          'collection_id' => $r->collection_id,
          'image'         => $path,
          'text'          => $r->text,
          'sort_order'    => $order,
        ]);

        return redirect()->route('admin.collection-sliders.index')
                         ->with('success','Đã thêm slider item.');
    }

    public function edit(CollectionSlider $collectionSlider)
    {
        $item = $collectionSlider;
        $collections = Collection::pluck('name','id');
        return view('admin.collection-sliders.form', compact('item','collections'));
    }

    public function update(Request $r, CollectionSlider $collectionSlider)
    {
        $r->validate([
          'collection_id' => 'required|exists:collections,id',
          'image'         => 'nullable|image|max:4096',
          'text'          => 'required|string',
        ]);

        $data = ['collection_id'=>$r->collection_id,'text'=>$r->text];
        if($r->hasFile('image')){
          $data['image'] = $r->file('image')->store('sliders','public');
        }
        $collectionSlider->update($data);

        return back()->with('success','Đã cập nhật slider item.');
    }

    public function destroy(CollectionSlider $collectionSlider)
    {
        $collectionSlider->delete();
        return back()->with('success','Đã xóa slider item.');
    }

    // Move up/down
    public function move(Request $r, CollectionSlider $collectionSlider)
    {
        $dir = $r->input('dir');
        $swap = CollectionSlider::where('sort_order', 
                  $dir==='up' ? '<' : '>', 
                  $collectionSlider->sort_order)
                ->orderBy('sort_order', $dir==='up' ? 'desc' : 'asc')
                ->first();
        if($swap){
          $a = $collectionSlider->sort_order;
          $collectionSlider->sort_order = $swap->sort_order;
          $swap->sort_order = $a;
          $collectionSlider->save();
          $swap->save();
        }
        return back();
    }
}
