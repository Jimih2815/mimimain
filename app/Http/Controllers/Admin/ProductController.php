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
    // 1. Validate, bao gồm file option_img
    $validated = $r->validate([
        'name'                              => 'required|string|max:255',
        'slug'                              => 'required|string|unique:products,slug',
        'description'                       => 'nullable|string',
        'base_price'                        => 'required|numeric',
        'img'                               => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'sub_img.*'                         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'options'                           => 'array',
        'options.*.name'                    => 'required|string',
        'options.*.values'                  => 'array',
        'options.*.values.*.value'          => 'required|string',
        'options.*.values.*.extra_price'    => 'required|numeric',
        'options.*.values.*.option_img'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // 2. Lưu ảnh chính & phụ
    $validated['img'] = $r->file('img')->store('products/main', 'public');
    $validated['sub_img'] = [];
    foreach ($r->file('sub_img') ?? [] as $file) {
        $validated['sub_img'][] = $file->store('products/sub', 'public');
    }

    // 3. Tạo product
    $product = Product::create($validated);

    // 4. Xử lý Options + upload ảnh mỗi giá trị
    $optionsInput = $r->input('options', []);
    $optionsFiles = $r->file('options', []);

    foreach ($optionsInput as $optIdx => $optData) {
        $optType = OptionType::create(['name' => $optData['name']]);
    
        foreach ($optData['values'] as $valIdx => $val) {
            // nếu user tải file mới, lấy file; nếu không, dùng existing_img
            $file    = $optionsFiles[$optIdx]['values'][$valIdx]['option_img'] ?? null;
            $imgPath = $file
                ? $file->store('products/options', 'public')
                : ($val['existing_img'] ?? null);
    
            $optVal = OptionValue::create([
                'option_type_id' => $optType->id,
                'value'          => $val['value'],
                'extra_price'    => $val['extra_price'],
                'option_img'     => $imgPath,
            ]);
    
            $product->optionValues()->attach($optVal->id);
        }
    }

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
    // 1. Validate, thêm 2 rule cho option_img & existing_img
    $validated = $r->validate([
        'name'                              => 'required|string|max:255',
        'slug'                              => "required|string|unique:products,slug,{$product->id}",
        'description'                       => 'nullable|string',
        'base_price'                        => 'required|numeric',
        'img'                               => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'sub_img.*'                         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'options'                           => 'array',
        'options.*.name'                    => 'required|string',
        'options.*.values'                  => 'array',
        'options.*.values.*.value'          => 'required|string',
        'options.*.values.*.extra_price'    => 'required|numeric',
        'options.*.values.*.option_img'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'options.*.values.*.existing_img'   => 'nullable|string',
    ]);

    // 2. Xử lý ảnh chính nếu có
    if ($r->hasFile('img')) {
        $validated['img'] = $r->file('img')->store('products/main', 'public');
    }

    // 3. Xử lý ảnh phụ nếu có
    if ($r->hasFile('sub_img')) {
        $subs = [];
        foreach ($r->file('sub_img') as $f) {
            $subs[] = $f->store('products/sub', 'public');
        }
        $validated['sub_img'] = $subs;
    }

    // Lấy input & files cho options
    $optionsInput = $r->input('options', []);
    $optionsFiles = $r->file('options', []);

    DB::transaction(function() use($validated, $product, $optionsInput, $optionsFiles) {
        // A. Cập nhật product
        $product->update([
            'name'        => $validated['name'],
            'slug'        => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'base_price'  => $validated['base_price'],
            'img'         => $validated['img'] ?? $product->img,
            'sub_img'     => $validated['sub_img'] ?? $product->sub_img,
        ]);

        // B. Xoá hoàn toàn option cũ
        $oldIds     = $product->optionValues()->pluck('option_values.id')->toArray();
        $oldTypeIds = OptionValue::whereIn('id', $oldIds)
                                 ->pluck('option_type_id')
                                 ->toArray();
        $product->optionValues()->detach();
        OptionValue::whereIn('id', $oldIds)->delete();
        OptionType::whereIn('id', $oldTypeIds)->delete();

        // C. Tạo lại options + ảnh tuỳ chọn
        foreach ($optionsInput as $optIdx => $optData) {
            $optType = OptionType::create([
                'name' => $optData['name'],
            ]);

            foreach ($optData['values'] as $valIdx => $val) {
                // nếu có file upload mới thì lưu, không thì giữ existing_img
                $file    = $optionsFiles[$optIdx]['values'][$valIdx]['option_img'] ?? null;
                $imgPath = $file
                    ? $file->store('products/options', 'public')
                    : ($val['existing_img'] ?? null);

                $optVal = OptionValue::create([
                    'option_type_id' => $optType->id,
                    'value'          => $val['value'],
                    'extra_price'    => $val['extra_price'],
                    'option_img'     => $imgPath,
                ]);

                $product->optionValues()->attach($optVal->id);
            }
        }
    });

    return redirect()
           ->route('admin.products.index')
           ->with('success', 'Cập nhật sản phẩm thành công!');
}

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Đã xóa sản phẩm.');
    }
}
