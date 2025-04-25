<?php

namespace App\Http\Controllers;

use App\Models\HomePage;
use App\Models\CollectionSlider;
use App\Models\HomeSectionImage;
use App\Models\ProductSlider;    // ← import model ProductSlider

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ, truyền banner + sliders + section images + product sliders
     */
    public function index()
    {
        $home = HomePage::first();

        $sliders = CollectionSlider::with('collection')
            ->orderBy('sort_order')
            ->get();

        $sectionImages = HomeSectionImage::with('collection')
            ->orderBy('position')
            ->get();

        // <-- đây là biến bạn đang foreach trong Blade
        $productSliders = ProductSlider::with('product')
            ->orderBy('sort_order')
            ->get();

        // Đưa tất cả 4 biến xuống view
        return view('home', compact(
            'home',
            'sliders',
            'sectionImages',
            'productSliders'
        ));
    }
}
