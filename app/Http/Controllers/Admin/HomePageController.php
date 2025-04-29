<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\HandlesWebpUpload;
use App\Models\HomePage;
use App\Models\Collection;
use App\Models\CollectionSlider;
use App\Models\ProductSlider;
use App\Models\HomeSectionImage;

class HomePageController extends Controller
{
    use HandlesWebpUpload;

    /**
     * Hiển thị form chỉnh sửa Home Page
     */
    public function edit()
{
    $home               = HomePage::first();
    $collections        = Collection::pluck('name', 'id');
    $collectionSliders  = CollectionSlider::with('collection')
                            ->orderBy('sort_order')
                            ->get();
    $productSliders     = ProductSlider::with('product')
                            ->orderBy('sort_order')
                            ->get();
    // Lấy về tất cả Home Section Images để preview, dùng position thay sort_order
    $homeSectionImages  = HomeSectionImage::orderBy('position')->get();

    return view('admin.home.edit', compact(
        'home',
        'collections',
        'collectionSliders',
        'productSliders',
        'homeSectionImages'
    ));
}

    /**
     * Xử lý submit cập nhật Home Page
     */
    public function update(Request $r)
    {
        $data = $r->validate([
            // — Phần Khởi Đầu (intro) —
            'intro_text'                          => 'nullable|string|max:255',
            'intro_button_text'                   => 'nullable|string|max:50',
            'intro_button_collection_id'          => 'nullable|exists:collections,id',

            // — Tiêu đề slider (Collection & Product) —
            'collection_slider_title'             => 'nullable|string|max:255',
            'product_slider_title'                => 'nullable|string|max:255',

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

        // Checkbox trả về true/false
        $data['show_button'] = $r->boolean('show_button');

        // Nếu có banner mới thì convert & upload WebP
        if ($r->hasFile('banner_image')) {
            $data['banner_image'] = $this->uploadAsWebp(
                $r->file('banner_image'),
                'home'
            );
        }

        HomePage::first()->update($data);

        return redirect()
            ->route('admin.home.edit')
            ->with('success', 'Cập nhật Home Page thành công');
    }
}
