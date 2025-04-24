<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\OptionType;
use App\Models\OptionValue;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $r)
    {
        // 1. Validate tất cả, bao gồm image chính + phụ
        $validated = $r->validate([
            'name'                         => 'required|string|max:255',
            'slug'                         => 'required|string|unique:products,slug',
            'description'                  => 'nullable|string',
            'base_price'                   => 'required|numeric',
            'img'                          => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sub_img.*'                    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'options'                      => 'array',
            'options.*.name'               => 'required|string',
            'options.*.values'             => 'array',
            'options.*.values.*.value'     => 'required|string',
            'options.*.values.*.extra_price'=> 'required|numeric',
        ]);

        // 2. Lưu file lên storage/public
        //  - ảnh chính
        $validated['img'] = $r->file('img')->store('products/main', 'public');

        //  - ảnh phụ (mảng)
        $subArr = [];
        if ($r->hasFile('sub_img')) {
            foreach ($r->file('sub_img') as $file) {
                $subArr[] = $file->store('products/sub', 'public');
            }
        }
        $validated['sub_img'] = $subArr;

        // 3. Tạo product + options trong transaction
        DB::transaction(function() use($validated) {
            $p = Product::create([
                'name'        => $validated['name'],
                'slug'        => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'base_price'  => $validated['base_price'],
                'img'         => $validated['img'],
                'sub_img'     => $validated['sub_img'],
            ]);

            // Options
            foreach ($validated['options'] ?? [] as $optData) {
                $optType = OptionType::create([
                    'name' => $optData['name'],
                ]);

                foreach ($optData['values'] as $val) {
                    $optVal = OptionValue::create([
                        'option_type_id' => $optType->id,
                        'value'          => $val['value'],
                        'extra_price'    => $val['extra_price'],
                    ]);
                    $p->optionValues()->attach($optVal->id);
                }
            }
        });

        return redirect()->route('admin.products.index')
                         ->with('success', 'Tạo sản phẩm thành công!');
    }

    public function edit(Product $product)
    {
        $product->load('optionValues.type');
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $r, Product $product)
    {
        $validated = $r->validate([
            'name'                         => 'required|string|max:255',
            'slug'                         => "required|string|unique:products,slug,{$product->id}",
            'description'                  => 'nullable|string',
            'base_price'                   => 'required|numeric',
            'img'                          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sub_img.*'                    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'options'                      => 'array',
            'options.*.name'               => 'required|string',
            'options.*.values'             => 'array',
            'options.*.values.*.value'     => 'required|string',
            'options.*.values.*.extra_price'=> 'required|numeric',
        ]);

        // Nếu thay ảnh chính
        if ($r->hasFile('img')) {
            $validated['img'] = $r->file('img')->store('products/main', 'public');
        }

        // Nếu upload mới ảnh phụ thì ghi đè
        if ($r->hasFile('sub_img')) {
            $subArr = [];
            foreach ($r->file('sub_img') as $file) {
                $subArr[] = $file->store('products/sub', 'public');
            }
            $validated['sub_img'] = $subArr;
        }

        DB::transaction(function() use($validated, $product) {
            // 1. Cập nhật product
            $product->update([
                'name'        => $validated['name'],
                'slug'        => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'base_price'  => $validated['base_price'],
                'img'         => $validated['img'] ?? $product->img,
                'sub_img'     => $validated['sub_img'] ?? $product->sub_img,
            ]);

            // 2. Xoá options cũ
            $oldIds = $product->optionValues()->pluck('option_values.id')->toArray();
            $oldTypeIds = OptionValue::whereIn('id', $oldIds)
                                     ->pluck('option_type_id')
                                     ->toArray();
            $product->optionValues()->detach();
            OptionValue::whereIn('id', $oldIds)->delete();
            OptionType::whereIn('id', $oldTypeIds)->delete();

            // 3. Thêm lại options mới
            foreach ($validated['options'] ?? [] as $optData) {
                $optType = OptionType::create(['name' => $optData['name']]);
                foreach ($optData['values'] as $val) {
                    $optVal = OptionValue::create([
                        'option_type_id' => $optType->id,
                        'value'          => $val['value'],
                        'extra_price'    => $val['extra_price'],
                    ]);
                    $product->optionValues()->attach($optVal->id);
                }
            }
        });

        return redirect()->route('admin.products.index')
                         ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Đã xóa sản phẩm.');
    }
}
