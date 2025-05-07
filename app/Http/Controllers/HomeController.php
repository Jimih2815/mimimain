<?php

namespace App\Http\Controllers;

use App\Models\HomePage;
use App\Models\CollectionSlider;
use App\Models\HomeSectionImage;
use App\Models\ProductSlider;



class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ, truyền banner + collection sliders +
     * section images + product sliders
     */
    public function index()
    {
        // 1) Cấu hình chung trang chủ
        $home = HomePage::first(); // chứa banner_image, about_title, about_text,...

        // 2) Slider bộ sưu tập
        $sliders = CollectionSlider::with('collection')
            ->orderBy('sort_order')
            ->get();

        // 3) Ảnh section (váo phần 7a)
        $sectionImages = HomeSectionImage::with('collection')
            ->orderBy('position')
            ->get();

        // 4) Slider sản phẩm
        $productSliders = ProductSlider::with('product')
            ->orderBy('sort_order')
            ->get();

        return $this->renderView('home', compact(
            'home',
            'sliders',
            'sectionImages',
            'productSliders'
        ));
    }
}
