<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Collection;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Hiển thị danh sách tin tức và slider sản phẩm (nếu có collection được chọn)
     */
    public function index(Request $request)
    {
        // 1) Lấy danh sách bài viết, sắp xếp mới nhất, phân trang 10 bản ghi/trang
        $posts = News::latest()->paginate(10);

        // 2) Lấy ID collection đã chọn từ session (do Admin lưu)
        $selectedId = session('news_selected_collection');

        // 3) Nếu có ID thì load luôn quan hệ products để render slider
        $selectedCollection = $selectedId
            ? Collection::with('products')->find($selectedId)
            : null;

        // 4) Trả về view với 2 biến: posts và selectedCollection
        return view('news.index', compact('posts', 'selectedCollection'));
    }

    /**
     * Hiển thị chi tiết một bài tin theo slug
     */
    public function show($slug)
    {
        // Tìm bài viết theo slug hoặc 404 nếu không có
        $post = News::where('slug', $slug)->firstOrFail();

        return view('news.show', compact('post'));
    }
}
