<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HandlesWebpUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    /**
     * Xử lý upload ảnh cho TinyMCE (product long description)
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:4096',
        ]);

        // Cho phép client chỉ định folder (dùng cho: mô tả, ảnh chính, ảnh phụ, option...)
        $folder = $request->input('folder', 'products/descriptions');

        // Whitelist folder để tránh bị truyền bậy
        $allowed = [
            'products/descriptions',
            'products/main',
            'products/sub',
            'products/options',
        ];
        if (!in_array($folder, $allowed, true)) {
            $folder = 'products/descriptions';
        }

        $relativePath = $this->uploadAsWebp($request->file('file'), $folder);

        return response()->json([
            // TinyMCE cần key 'location'
            'location' => asset('storage/' . $relativePath),

            // Admin form upload nhanh cần 'path' để lưu DB
            'path' => $relativePath,
        ]);
    }


    public function store(Request $request)
    {
        // 0) Loại bỏ các dòng trống trong options
        $input = $request->all();
        if (!empty($input['options'])) {
            $input['options'] = $this->filterOptions($input['options']);
            $request->replace($input);
        }

        // 1) Validate
        $validated = $request->validate([
            'name'                      => 'required|string|max:255',
            'slug'                      => 'required|string|unique:products,slug',
            'description'               => 'nullable|string',
            'long_description'          => 'nullable|string',
            'base_price'                => 'required|numeric',
            'img'                       => 'nullable|image|max:4096',
            'img_existing'              => 'required_without:img|nullable|string',
            'sub_img.*'                 => 'nullable|image|max:4096',
            'sub_img_existing'          => 'array',
            'sub_img_existing.*'        => 'string',
            'options'                   => 'array',
            'options.*.name'            => 'required|string',
            'options.*.values'          => 'array',
            'options.*.values.*.value'       => 'required|string',
            'options.*.values.*.extra_price' => 'required|numeric',
            'options.*.values.*.option_img'  => 'nullable|image|max:4096',
            'options.*.values.*.existing_img'=> 'nullable|string',
        ]);

        // 2) Ảnh: ưu tiên dùng file nếu submit có file, còn không thì dùng path đã upload trước
        if ($request->hasFile('img')) {
            $validated['img'] = $this->uploadAsWebp($request->file('img'), 'products/main');
        } else {
            $validated['img'] = $request->input('img_existing');
        }

        // Ảnh phụ: nhận danh sách path đã upload trước + append file mới (nếu có)
        $validated['sub_img'] = $request->input('sub_img_existing', []);
        foreach ($request->file('sub_img') ?? [] as $file) {
            $validated['sub_img'][] = $this->uploadAsWebp($file, 'products/sub');
        }

// 3) Tạo product
        $product = Product::create($validated);

        // 4) Sync options
        $this->syncOptions($product, $validated['options'] ?? [], $request->file('options', []));

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Tạo sản phẩm thành công!');
    }

    public function edit(Product $product)
    {
        $product->load('optionValues.type');
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // 0) Loại bỏ các dòng trống trong options
        $input = $request->all();
        if (!empty($input['options'])) {
            $input['options'] = $this->filterOptions($input['options']);
            $request->replace($input);
        }

        // 1) Validate
        $validated = $request->validate([
            'name'                      => 'required|string|max:255',
            'slug'                      => "required|string|unique:products,slug,{$product->id}",
            'description'               => 'nullable|string',
            'long_description'          => 'nullable|string',
            'base_price'                => 'required|numeric',
            'img'                       => 'nullable|image|max:4096',
            'img_existing'              => 'nullable|string',
            'sub_img.*'                 => 'nullable|image|max:4096',
            'sub_img_existing'          => 'array',
            'sub_img_existing.*'        => 'string',
            'options'                   => 'array',
            'options.*.name'            => 'required|string',
            'options.*.values'          => 'array',
            'options.*.values.*.value'       => 'required|string',
            'options.*.values.*.extra_price' => 'required|numeric',
            'options.*.values.*.option_img'  => 'nullable|image|max:4096',
            'options.*.values.*.existing_img'=> 'nullable|string',
        ]);

        // 2) Ảnh: ưu tiên file nếu submit có file, hoặc dùng path đã upload trước (img_existing)
        if ($request->hasFile('img')) {
            $validated['img'] = $this->uploadAsWebp($request->file('img'), 'products/main');
        } elseif ($request->filled('img_existing')) {
            $validated['img'] = $request->input('img_existing');
        }

        // Ảnh phụ: nếu có sub_img_existing (tức UI đang quản lý list) thì dùng list đó trước,
        // sau đó append thêm file mới nếu có
        if ($request->has('sub_img_existing')) {
            $validated['sub_img'] = $request->input('sub_img_existing', []);
        }

        foreach ($request->file('sub_img') ?? [] as $f) {
            $validated['sub_img'] = $validated['sub_img'] ?? ($product->sub_img ?? []);
            $validated['sub_img'][] = $this->uploadAsWebp($f, 'products/sub');
        }

// Lưu ID cũ trước khi detach để xóa đúng
        $oldOptionValueIds = $product
          ->optionValues()
          ->pluck('option_values.id')
          ->toArray();

        $oldOptionTypeIds = OptionValue
          ::whereIn('id', $oldOptionValueIds)
          ->pluck('option_type_id')
          ->toArray();

        DB::transaction(function() use ($product, $validated, $request, $oldOptionValueIds, $oldOptionTypeIds) {
            // A) Cập nhật product
            $product->update([
                'name'             => $validated['name'],
                'slug'             => $validated['slug'],
                'description'      => $validated['description'] ?? null,
                'long_description' => $validated['long_description'] ?? null,
                'base_price'       => $validated['base_price'],
                'img'              => $validated['img'] ?? $product->img,
                'sub_img'          => $validated['sub_img'] ?? $product->sub_img,
            ]);

            // B) Xóa option cũ
            $product->optionValues()->detach();
            OptionValue::whereIn('id', $oldOptionValueIds)->delete();
            OptionType::whereIn('id', $oldOptionTypeIds)->delete();

            // C) Tạo & attach lại options mới
            $this->syncOptions($product, $validated['options'] ?? [], $request->file('options', []));
        });

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Đã xóa sản phẩm.');
    }

    /**
     * Loại bỏ các option/value hoàn toàn trống
     */
    private function filterOptions(array $opts): array
    {
        $out = [];
        foreach ($opts as $opt) {
            $vals = array_filter($opt['values'], function($v) {
                return trim($v['value'] ?? '') !== '' 
                    || trim((string)$v['extra_price'] ?? '') !== '';
            });
            if (count($vals)) {
                $opt['values'] = array_values($vals);
                $out[] = $opt;
            }
        }
        return $out;
    }

    /**
     * Tạo & attach options cho product
     */
    private function syncOptions(Product $product, array $opts, array $files): void
    {
        foreach ($opts as $i => $opt) {
            $type = OptionType::create(['name' => $opt['name']]);
            foreach ($opt['values'] as $j => $val) {
                $imgFile = $files[$i]['values'][$j]['option_img'] ?? null;
                $path    = $imgFile
                    ? $this->uploadAsWebp($imgFile, 'products/options')
                    : ($val['existing_img'] ?? null);

                $ov = OptionValue::create([
                    'option_type_id' => $type->id,
                    'value'          => $val['value'],
                    'extra_price'    => $val['extra_price'],
                    'option_img'     => $path,
                ]);

                $product->optionValues()->attach($ov->id);
            }
        }
    }
    
}
