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
use App\Models\HomeBannerImage;
use Illuminate\Support\Facades\Storage;

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

    // Banner slider images
    $bannerImagesDesktop = HomeBannerImage::with('collection')
        ->where('device', 'desktop')
        ->orderBy('sort_order')
        ->get();

    $bannerImagesMobile = HomeBannerImage::with('collection')
        ->where('device', 'mobile')
        ->orderBy('sort_order')
        ->get();

    return view('admin.home.edit', compact(
        'home',
        'collections',
        'collectionSliders',
        'productSliders',
        'homeSectionImages',
        'bannerImagesDesktop',
        'bannerImagesMobile'
    ));
}

    /**
     * Xử lý submit cập nhật Home Page
     */
    public function update(Request $r)
    {
        $home = HomePage::first();

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

            // — Banner (legacy single) & About —
            'banner_image'                        => 'nullable|image|max:4096',
            'banner_image_mobile'                 => 'nullable|image|max:4096',
            'popup_image'                         => 'nullable|image|max:4096',

            // — Banner slider (multiple) —
            'banner_images_desktop'               => 'nullable|array',
            'banner_images_desktop.*'             => 'nullable|image|max:4096',
            'banner_images_mobile'                => 'nullable|array',
            'banner_images_mobile.*'              => 'nullable|image|max:4096',
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

        // Popup image (ưu tiên file upload, nếu không có thì có thể xóa)
        if ($r->hasFile('popup_image')) {
            if ($home->popup_image) {
                Storage::disk('public')->delete($home->popup_image);
            }
            $data['popup_image'] = $this->uploadAsWebp(
                $r->file('popup_image'),
                'home/popup'
            );
        } elseif ($r->boolean('popup_image_remove')) {
            if ($home->popup_image) {
                Storage::disk('public')->delete($home->popup_image);
            }
            $data['popup_image'] = null;
        }

        
        // Nếu có banner mobile mới thì convert & upload WebP
        if ($r->hasFile('banner_image_mobile')) {
            $data['banner_image_mobile'] = $this->uploadAsWebp(
                $r->file('banner_image_mobile'),
                'home'
            );
        }
        $home->update($data);

        // =============================
        // Banner slider: update existing
        // =============================
        $desktopItems = $r->input('banner_items_desktop', []);
        foreach ($desktopItems as $id => $row) {
            $banner = HomeBannerImage::where('device', 'desktop')->find($id);
            if (!$banner) continue;

            if (!empty($row['delete'])) {
                if ($banner->image) {
                    Storage::disk('public')->delete($banner->image);
                }
                $banner->delete();
                continue;
            }

            $banner->collection_id = $row['collection_id'] ?? null;
            $banner->save();
        }

        $mobileItems = $r->input('banner_items_mobile', []);
        foreach ($mobileItems as $id => $row) {
            $banner = HomeBannerImage::where('device', 'mobile')->find($id);
            if (!$banner) continue;

            if (!empty($row['delete'])) {
                if ($banner->image) {
                    Storage::disk('public')->delete($banner->image);
                }
                $banner->delete();
                continue;
            }

            $banner->collection_id = $row['collection_id'] ?? null;
            $banner->save();
        }

        // =============================
        // Banner slider: upload new
        // =============================
        if ($r->hasFile('banner_images_desktop')) {
            $max = (int) HomeBannerImage::where('device', 'desktop')->max('sort_order');
            foreach ($r->file('banner_images_desktop', []) as $file) {
                if (!$file) continue;
                $path = $this->uploadAsWebp($file, 'home/banners');
                HomeBannerImage::create([
                    'home_page_id' => $home->id,
                    'device' => 'desktop',
                    'image' => $path,
                    'collection_id' => null,
                    'sort_order' => ++$max,
                ]);
            }
        }

        if ($r->hasFile('banner_images_mobile')) {
            $max = (int) HomeBannerImage::where('device', 'mobile')->max('sort_order');
            foreach ($r->file('banner_images_mobile', []) as $file) {
                if (!$file) continue;
                $path = $this->uploadAsWebp($file, 'home/banners');
                HomeBannerImage::create([
                    'home_page_id' => $home->id,
                    'device' => 'mobile',
                    'image' => $path,
                    'collection_id' => null,
                    'sort_order' => ++$max,
                ]);
            }
        }

        return redirect()
            ->route('admin.home.edit')
            ->with('success', 'Cập nhật Home Page thành công');
    }

    /**
     * Ajax reorder for banner slider
     * payload: { device: 'desktop'|'mobile', ids: [1,2,3] }
     */
    public function reorderBanners(Request $r)
    {
        $validated = $r->validate([
            'device' => 'required|in:desktop,mobile',
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        foreach ($validated['ids'] as $idx => $id) {
            HomeBannerImage::where('device', $validated['device'])
                ->where('id', $id)
                ->update(['sort_order' => $idx + 1]);
        }

        return response()->json(['status' => 'success']);
    }
}
