<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CategoryHeader;
use App\Models\Product;

class HeaderController extends Controller
{
    public function index()
    {
        $categories = Category::with('headers.products')->get();
        $products   = Product::all();
        return view('admin.headers.index', compact('categories','products'));
    }

    public function store(Request $r)
    {
        $r->validate([
          'category_id' => 'required|exists:categories,id',
          'title'       => 'required|string',
        ]);
        CategoryHeader::create([
          'category_id'=> $r->category_id,
          'title'      => $r->title,
          'sort_order' => CategoryHeader::where('category_id',$r->category_id)
                                        ->max('sort_order') + 1,
        ]);
        return back()->with('success','Thêm header mới thành công');
    }

    public function update(Request $r, CategoryHeader $header)
    {
        $r->validate(['title'=>'required|string']);
        $header->update(['title'=>$r->title,'sort_order'=>$r->sort_order]);
        return back()->with('success','Cập nhật header thành công');
    }

    public function destroy(CategoryHeader $header)
    {
        $header->delete();
        return back()->with('success','Xóa header thành công');
    }

    // ADD single product
    public function addProduct(Request $r, CategoryHeader $header)
    {
        $r->validate(['product_id'=>'required|exists:products,id']);
        // tránh duplicate
        if (! $header->products->contains($r->product_id)) {
            $header->products()->attach($r->product_id);
        }
        return back()->with('success','Đã thêm sản phẩm vào header');
    }

    // REMOVE single product
    public function removeProduct(CategoryHeader $header, $pid)
    {
        $header->products()->detach($pid);
        return back()->with('success','Đã xóa sản phẩm khỏi header');
    }
}
