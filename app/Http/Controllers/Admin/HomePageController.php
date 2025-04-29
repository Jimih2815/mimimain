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
        $collections = Collection::pluck('name', 'id');
        return view('admin.home.edit', compact('home', 'collections'));
    }

    /**
     * Xử lý submit cập nhật Home Page
     */
    public function update(Request $r)
    {
        // 1) Validate tất cả các trường dynamic
        $data = $r->validate([
            // — Phần Khởi Đầu (intro) —
            'intro_text'                          => 'nullable|string|max:255',
            'intro_button_text'                   => 'nullable|string|max:50',
            'intro_button_collection_id'          => 'nullable|exists:collections,id',
            'collection_slider_title'              => 'nullable|string|max:255',
            // — Phần trước Banner (pre_banner) —
            'pre_banner_title'                    => 'nullable|string|max:100',
            'pre_banner_button_text'              => 'nullable|string|max:50',
            'pre_banner_button_collection_id'     => 'nullable|exists:collections,id',

            // — Phần Bộ Sưu Tập (collection_section) —
            'collection_section_title'                => 'nullable|string|max:255',
            'collection_section_button_text'          => 'nullable|string|max:50',
            'collection_section_button_collection_id' => 'nullable|exists:collections,id',

            // — Banner chính & About —
            'banner_image'                        => 'nullable|image|max:4096',
            'about_title'                         => 'nullable|string|max:255',
            'about_text'                          => 'nullable|string',

            // — Nút trung tâm —
            'show_button'                         => 'sometimes',
            'button_collection_id'                => 'nullable|exists:collections,id',
            'button_text'                         => 'nullable|string|max:50',
        ]);

        // 2) Xử lý checkbox cho nút trung tâm
        $data['show_button'] = $r->has('show_button');

        // 3) Xử lý upload ảnh Banner nếu có
        if ($r->hasFile('banner_image')) {
            $data['banner_image'] = $r
                ->file('banner_image')
                ->store('home', 'public');
        }

        // 4) Cập nhật vào DB
        HomePage::first()->update($data);

        // 5) Chuyển về form với thông báo thành công
        return redirect()
            ->route('admin.home.edit')
            ->with('success', 'Cập nhật Home Page thành công');
    }
}
