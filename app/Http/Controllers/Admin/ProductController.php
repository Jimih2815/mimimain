<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HandlesWebpUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\OptionType;
use App\Models\OptionValue;

class ProductController extends Controller
{
    use HandlesWebpUpload;

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
    // 0) Lọc bỏ các dòng trống trong options trước khi validate
    $input = $r->all();
    if (! empty($input['options']) && is_array($input['options'])) {
        $filteredOptions = [];
        foreach ($input['options'] as $optIndex => $opt) {
            if (! empty($opt['values']) && is_array($opt['values'])) {
                $filteredValues = [];
                foreach ($opt['values'] as $val) {
                    $hasValue = trim($val['value'] ?? '') !== '';
                    $hasExtra = trim((string)($val['extra_price'] ?? '')) !== '';
                    if ($hasValue || $hasExtra) {
                        $filteredValues[] = $val;
                    }
                }
                if (count($filteredValues) > 0) {
                    $opt['values'] = $filteredValues;
                    $filteredOptions[$optIndex] = $opt;
                }
            }
        }
        $input['options'] = array_values($filteredOptions);
    }
    $r->replace($input);

    // 1) Validate
    $validated = $r->validate([
        'name'                              => 'required|string|max:255',
        'slug'                              => 'required|string|unique:products,slug',
        'description'                       => 'nullable|string',
        'base_price'                        => 'required|numeric',
        'img'                               => 'required|image|max:4096',
        'sub_img.*'                         => 'nullable|image|max:4096',
        'options'                           => 'array',
        'options.*.name'                    => 'required|string',
        'options.*.values'                  => 'array',
        'options.*.values.*.value'          => 'required|string',
        'options.*.values.*.extra_price'    => 'required|numeric',
        'options.*.values.*.option_img'     => 'nullable|image|max:4096',
    ]);

    // 2) Upload ảnh chính & phụ
    $validated['img'] = $this->uploadAsWebp($r->file('img'), 'products/main');
    $validated['sub_img'] = [];
    foreach ($r->file('sub_img') ?? [] as $file) {
        $validated['sub_img'][] = $this->uploadAsWebp($file, 'products/sub');
    }

    // 3) Tạo product
    $product = Product::create($validated);

    // 4) Xử lý options
    $optionsInput = $validated['options'] ?? [];
    $optionsFiles = $r->file('options', []);

    foreach ($optionsInput as $optIdx => $optData) {
        $optType = OptionType::create(['name' => $optData['name']]);
        foreach ($optData['values'] as $valIdx => $val) {
            $file = $optionsFiles[$optIdx]['values'][$valIdx]['option_img'] ?? null;
            $imgPath = $file
                ? $this->uploadAsWebp($file, 'products/options')
                : null;

            $optVal = OptionValue::create([
                'option_type_id' => $optType->id,
                'value'          => $val['value'],
                'extra_price'    => $val['extra_price'],
                'option_img'     => $imgPath,
            ]);
            $product->optionValues()->attach($optVal->id);
        }
    }

    return redirect()
        ->route('admin.products.edit', $product)
        ->with('success', 'Tạo sản phẩm thành công!');
}


    public function edit(Product $product)
    {
        $product->load('optionValues.type');
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $r, Product $product)
{
    // 0) Lọc bỏ các dòng trống trong options trước khi validate
    $input = $r->all();
    if (! empty($input['options']) && is_array($input['options'])) {
        $filteredOptions = [];
        foreach ($input['options'] as $optIndex => $opt) {
            if (! empty($opt['values']) && is_array($opt['values'])) {
                $filteredValues = [];
                foreach ($opt['values'] as $val) {
                    $hasValue = trim($val['value'] ?? '') !== '';
                    $hasExtra = trim((string)($val['extra_price'] ?? '')) !== '';
                    if ($hasValue || $hasExtra) {
                        $filteredValues[] = $val;
                    }
                }
                if (count($filteredValues) > 0) {
                    // giữ option này nếu còn ít nhất 1 value hợp lệ
                    $opt['values'] = $filteredValues;
                    $filteredOptions[$optIndex] = $opt;
                }
            }
        }
        // re-index mảng cho gọn
        $input['options'] = array_values($filteredOptions);
    }
    // replace request data để validate & xử lý tiếp
    $r->replace($input);

    // 1) Validate
    $validated = $r->validate([
        'name'                              => 'required|string|max:255',
        'slug'                              => "required|string|unique:products,slug,{$product->id}",
        'description'                       => 'nullable|string',
        'base_price'                        => 'required|numeric',
        'img'                               => 'nullable|image|max:4096',
        'sub_img.*'                         => 'nullable|image|max:4096',
        'options'                           => 'array',
        'options.*.name'                    => 'required|string',
        'options.*.values'                  => 'array',
        'options.*.values.*.value'          => 'required|string',
        'options.*.values.*.extra_price'    => 'required|numeric',
        'options.*.values.*.option_img'     => 'nullable|image|max:4096',
        'options.*.values.*.existing_img'   => 'nullable|string',
    ]);

    // 2) Xử lý ảnh chính & phụ
    if ($r->hasFile('img')) {
        $validated['img'] = $this->uploadAsWebp(
            $r->file('img'),
            'products/main'
        );
    }
    if ($r->hasFile('sub_img')) {
        $subs = [];
        foreach ($r->file('sub_img') as $f) {
            $subs[] = $this->uploadAsWebp($f, 'products/sub');
        }
        $validated['sub_img'] = $subs;
    }

    // 3) Chuẩn bị dữ liệu options
    $optionsInput = $r->input('options', []);
    $optionsFiles = $r->file('options', []);

    // 4) Transaction: update product, xóa cũ, tạo mới
    DB::transaction(function() use($validated, $product, $optionsInput, $optionsFiles) {
        // A) Cập nhật product
        $product->update([
            'name'        => $validated['name'],
            'slug'        => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'base_price'  => $validated['base_price'],
            'img'         => $validated['img'] ?? $product->img,
            'sub_img'     => $validated['sub_img'] ?? $product->sub_img,
        ]);

        // B) Xóa toàn bộ OptionValue & OptionType cũ
        $oldIds     = $product->optionValues()->pluck('option_values.id')->toArray();
        $oldTypeIds = OptionValue::whereIn('id', $oldIds)
                                 ->pluck('option_type_id')
                                 ->toArray();

        $product->optionValues()->detach();
        OptionValue::whereIn('id', $oldIds)->delete();
        OptionType::whereIn('id', $oldTypeIds)->delete();

        // C) Tạo lại OptionType & OptionValue, rồi attach
        foreach ($optionsInput as $optIdx => $optData) {
            $optType = OptionType::create([
                'name' => $optData['name'],
            ]);

            foreach ($optData['values'] as $valIdx => $val) {
                $file = $optionsFiles[$optIdx]['values'][$valIdx]['option_img'] ?? null;

                $imgPath = $file
                    ? $this->uploadAsWebp($file, 'products/options')
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
        ->route('admin.products.edit', $product)
        ->with('success','Cập nhật sản phẩm thành công!');
}


    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Đã xóa sản phẩm.');
    }
}
