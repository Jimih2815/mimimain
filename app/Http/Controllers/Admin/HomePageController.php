<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomePage;

class HomePageController extends Controller
{
    // Form chỉnh Banner
    public function edit()
    {
        $home = HomePage::first();
        return view('admin.home.edit', compact('home'));
    }

    // Xử lý upload
    public function update(Request $r)
    {
        $r->validate([
            'banner_image' => 'required|image|max:4096',
        ]);

        $home = HomePage::first();
        // nếu có file mới, lưu vào storage/app/public/home
        $path = $r->file('banner_image')
                  ->store('home', 'public');
        $home->update(['banner_image' => $path]);

        return redirect()->route('admin.home.edit')
                         ->with('success','Banner đã được cập nhật!');
    }
}
