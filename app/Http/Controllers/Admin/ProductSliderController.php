<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HandlesWebpUpload;
use Illuminate\Http\Request;
use App\Models\ProductSlider;
use App\Models\Product;
use App\Models\HomePage;

class ProductSliderController extends Controller
{
    use HandlesWebpUpload;

    /**
     * INDEX: hiển thị danh sách slider và tiêu đề từ HomePage
     */
    public function index()
    {
        $sliders = ProductSlider::with('product')
                      ->orderBy('sort_order')
                      ->get();

        $home = HomePage::first();

        return view('admin.product-sliders.index', compact('sliders', 'home'));
    }

    /**
     * CREATE: form thêm mới
     */
    public function create()
    {
        $products = Product::all();
        return view('admin.product-sliders.create', compact('products'));
    }

    /**
     * STORE: lưu mới, convert ảnh sang WebP
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'image'      => 'nullable|image|max:4096',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $path = $this->uploadAsWebp(
                $request->file('image'),
                'sliders'
            );
        } else {
            $product = Product::findOrFail($data['product_id']);
            $path = $product->img;
        }

        ProductSlider::create([
            'product_id' => $data['product_id'],
            'image'      => $path,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()
               ->route('admin.product-sliders.index')
               ->with('success','Đã thêm slider thành công');
    }

    /**
     * EDIT: form sửa
     */
    public function edit(ProductSlider $product_slider)
    {
        $slider   = $product_slider;
        $products = Product::all();

        return view('admin.product-sliders.edit', compact('slider','products'));
    }

    /**
     * UPDATE: cập nhật, convert ảnh nếu có file mới
     */
    public function update(Request $request, ProductSlider $product_slider)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'image'      => 'nullable|image|max:4096',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $path = $this->uploadAsWebp(
                $request->file('image'),
                'sliders'
            );
        } else {
            $path = $product_slider->image;
        }

        $product_slider->update([
            'product_id' => $data['product_id'],
            'image'      => $path,
            'sort_order' => $data['sort_order'] ?? $product_slider->sort_order,
        ]);

        return redirect()
               ->route('admin.product-sliders.index')
               ->with('success','Đã cập nhật slider thành công');
    }

    /**
     * REORDER: nhận mảng IDs và cập nhật sort_order
     */
    public function reorder(Request $request)
    {
        $ids = $request->input('ids', []);
        foreach ($ids as $index => $id) {
            ProductSlider::where('id', $id)
                ->update(['sort_order' => $index + 1]);
        }
        return response()->json(['status' => 'success']);
    }

    /**
     * DESTROY: xóa
     */
    public function destroy(ProductSlider $product_slider)
    {
        $product_slider->delete();

        return redirect()
               ->route('admin.product-sliders.index')
               ->with('success','Đã xóa slider');
    }
}
