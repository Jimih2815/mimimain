<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomePage;

class HomePageController extends Controller
{
    /**
     * Hiển thị form chỉnh sửa Home Page
     */
    public function edit()
    {
        $home = HomePage::first();
        return view('admin.home.edit', compact('home'));
    }

    /**
     * Xử lý lưu banner và phần giới thiệu
     */
    public function update(Request $r)
    {
        $data = $r->validate([
            'banner_image' => 'nullable|image|max:4096',
            'about_title'  => 'nullable|string|max:255',
            'about_text'   => 'nullable|string',
        ]);

        $home = HomePage::first();

        // Nếu có file banner mới thì lưu
        if ($r->hasFile('banner_image')) {
            $path = $r->file('banner_image')->store('home', 'public');
            $data['banner_image'] = $path;
        }

        $home->update($data);

        return redirect()
            ->route('admin.home.edit')
            ->with('success','Cập nhật Home Page thành công');
    }
}
