<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductSlider;
use App\Models\Product;
use App\Models\HomePage;   // ← thêm import

class ProductSliderController extends Controller
{
    // 1) INDEX: hiển thị danh sách slider và tiêu đề từ HomePage
    public function index()
    {
        // load hết sliders kèm product để show tên và link
        $sliders = ProductSlider::with('product')
                      ->orderBy('sort_order')
                      ->get();

        // lấy HomePage để truy xuất product_slider_title
        $home = HomePage::first();

        return view('admin.product-sliders.index', compact('sliders', 'home'));
    }

    // 2) CREATE: form thêm mới
    public function create()
    {
        $products = Product::all();
        return view('admin.product-sliders.create', compact('products'));
    }

    // 3) STORE: lưu mới
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'image'      => 'nullable|image',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sliders', 'public');
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

    // 4) EDIT: form sửa
    public function edit(ProductSlider $product_slider)
    {
        $slider   = $product_slider;
        $products = Product::all();
        return view('admin.product-sliders.edit', compact('slider','products'));
    }

    // 5) UPDATE: cập nhật
    public function update(Request $request, ProductSlider $product_slider)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'image'      => 'nullable|image',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sliders', 'public');
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

    // 6) DESTROY: xóa
    public function destroy(ProductSlider $product_slider)
    {
        $product_slider->delete();
        return redirect()
               ->route('admin.product-sliders.index')
               ->with('success','Đã xóa slider');
    }
}
