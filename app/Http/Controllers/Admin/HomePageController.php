<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomePage;
use App\Models\Collection;

class HomePageController extends Controller
{
    /**
     * Hiển thị form chỉnh sửa Home Page
     */
    public function edit()
    {
        $home        = HomePage::first();
        $collections = Collection::pluck('name','id');
        return view('admin.home.edit', compact('home','collections'));
    }

    /**
     * Xử lý lưu banner, giới thiệu và button control
     */
    public function update(Request $r)
    {
        $data = $r->validate([
            'banner_image'          => 'nullable|image|max:4096',
            'about_title'           => 'nullable|string|max:255',
            'about_text'            => 'nullable|string',
            'show_button'           => 'sometimes|boolean',
            'button_collection_id'  => 'nullable|exists:collections,id',
        ]);

        // Checkbox chỉ có key khi checked
        $data['show_button'] = $r->has('show_button');

        // Xử lý banner image
        if ($r->hasFile('banner_image')) {
            $data['banner_image'] = $r
                ->file('banner_image')
                ->store('home','public');
        }

        HomePage::first()->update($data);

        return redirect()
            ->route('admin.home.edit')
            ->with('success','Cập nhật Home Page thành công');
    }
}
