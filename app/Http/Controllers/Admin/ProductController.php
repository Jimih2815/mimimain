<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\OptionType;
use App\Models\OptionValue;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm (15/trang)
     */
    public function index()
    {
        $products = Product::paginate(15);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Form thêm mới sản phẩm
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Lưu sản phẩm + options mới
     */
    public function store(Request $r)
    {
        $r->validate([
            'name'                         => 'required|string',
            'slug'                         => 'required|string|unique:products,slug',
            'description'                  => 'nullable|string',
            'base_price'                   => 'required|numeric',
            'options'                      => 'array',
            'options.*.name'               => 'required|string',
            'options.*.values'             => 'array',
            'options.*.values.*.value'     => 'required|string',
            'options.*.values.*.extra_price'=> 'required|numeric',
        ]);

        DB::transaction(function() use($r){
            // 1) Tạo product
            $p = Product::create($r->only(['name','slug','description','base_price']));

            // 2) Tạo OptionType → OptionValue → attach pivot
            foreach ($r->options ?? [] as $optData) {
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
                         ->with('success','Đã tạo product + options thành công!');
    }

    /**
     * Form sửa sản phẩm
     */
    public function edit(Product $product)
    {
        // Load luôn optionValues cùng type để show trong form
        $product->load('optionValues.type');
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Cập nhật sản phẩm + options
     */
    public function update(Request $r, Product $product)
    {
        $r->validate([
            'name'                         => 'required|string',
            'slug'                         => "required|string|unique:products,slug,{$product->id}",
            'description'                  => 'nullable|string',
            'base_price'                   => 'required|numeric',
            'options'                      => 'array',
            'options.*.name'               => 'required|string',
            'options.*.values'             => 'array',
            'options.*.values.*.value'     => 'required|string',
            'options.*.values.*.extra_price'=> 'required|numeric',
        ]);

        DB::transaction(function() use($r, $product){
            // 1) Cập nhật các trường cơ bản
            $product->update($r->only(['name','slug','description','base_price']));

            // 2) Xoá toàn bộ options cũ khỏi product + xoá luôn bản ghi OptionValue & OptionType
            $oldValueIds = $product->optionValues()->pluck('option_values.id')->toArray();
            $oldTypeIds  = OptionValue::whereIn('id', $oldValueIds)
                                      ->pluck('option_type_id')
                                      ->toArray();

            // detach pivot
            $product->optionValues()->detach();

            // xoá OptionValue + OptionType cũ
            OptionValue::whereIn('id', $oldValueIds)->delete();
            OptionType::whereIn('id', $oldTypeIds)->delete();

            // 3) Tạo lại OptionType → OptionValue → attach pivot
            foreach ($r->options ?? [] as $optData) {
                $optType = OptionType::create([
                    'name' => $optData['name'],
                ]);

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
                         ->with('success','Đã cập nhật sản phẩm thành công!');
    }

    /**
     * Xoá sản phẩm (và pivot tự cascadeOnDelete)
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success','Đã xoá product.');
    }
}
