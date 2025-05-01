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
            'file' => 'required|image|max:4096',            // chấp nhận mọi định dạng ảnh
        ]);
    
        // Dùng trait để convert & lưu webp vào storage/app/public/products/descriptions
        $relativePath = $this->uploadAsWebp(
            $request->file('file'),
            'products/descriptions'                         // folder tuỳ bạn đặt
        );
    
        // Trả về absolute URL để TinyMCE chèn vào editor
        return response()->json([
            'location' => asset('storage/' . $relativePath)
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
            'img'                       => 'required|image|max:4096',
            'sub_img.*'                 => 'nullable|image|max:4096',
            'options'                   => 'array',
            'options.*.name'            => 'required|string',
            'options.*.values'          => 'array',
            'options.*.values.*.value'       => 'required|string',
            'options.*.values.*.extra_price' => 'required|numeric',
            'options.*.values.*.option_img'  => 'nullable|image|max:4096',
        ]);

        // 2) Upload ảnh chính & phụ
        $validated['img'] = $this->uploadAsWebp($request->file('img'), 'products/main');
        $validated['sub_img'] = [];
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
            'sub_img.*'                 => 'nullable|image|max:4096',
            'options'                   => 'array',
            'options.*.name'            => 'required|string',
            'options.*.values'          => 'array',
            'options.*.values.*.value'       => 'required|string',
            'options.*.values.*.extra_price' => 'required|numeric',
            'options.*.values.*.option_img'  => 'nullable|image|max:4096',
            'options.*.values.*.existing_img'=> 'nullable|string',
        ]);

        // 2) Xử lý ảnh mới nếu có
        if ($request->hasFile('img')) {
            $validated['img'] = $this->uploadAsWebp($request->file('img'), 'products/main');
        }
        if ($request->hasFile('sub_img')) {
            $subs = [];
            foreach ($request->file('sub_img') as $f) {
                $subs[] = $this->uploadAsWebp($f, 'products/sub');
            }
            $validated['sub_img'] = $subs;
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
