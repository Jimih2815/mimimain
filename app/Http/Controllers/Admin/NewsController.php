<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    public function index()
    {
        $posts = News::latest()->paginate(15);
        return view('admin.news.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'title'     => 'required|string|max:255',
            'thumbnail' => 'nullable|image|max:2048',
            'content'   => 'required|string',
        ]);

        if ($req->hasFile('thumbnail')) {
            $data['thumbnail'] = $req->file('thumbnail')
                                    ->store('news', 'public');
        }

        News::create($data);

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Tạo tin tức thành công');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $req, News $news)
    {
        $data = $req->validate([
            'title'     => 'required|string|max:255',
            'thumbnail' => 'nullable|image|max:2048',
            'content'   => 'required|string',
        ]);

        if ($req->hasFile('thumbnail')) {
            // xoá ảnh cũ nếu có
            if ($news->thumbnail) {
                Storage::disk('public')->delete($news->thumbnail);
            }
            $data['thumbnail'] = $req->file('thumbnail')
                                    ->store('news', 'public');
        }

        $news->update($data);

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Cập nhật tin tức thành công');
    }

    public function destroy(News $news)
    {
        if ($news->thumbnail) {
            Storage::disk('public')->delete($news->thumbnail);
        }
        $news->delete();

        return back()->with('success', 'Xóa tin tức thành công');
    }

    /**
     * Xử lý upload ảnh từ TinyMCE
     */
    public function uploadImage(Request $request)
    {
        // TinyMCE mặc định gửi key 'file'
        if (! $request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        try {
            // lưu vào storage/app/public/news
            $path = $file->store('news', 'public');
            $url  = Storage::url($path);
            return response()->json(['location' => $url], 200);
        } catch (\Throwable $e) {
            Log::error('TinyMCE upload failed: '.$e->getMessage());
            return response()->json(['error' => 'Upload failed'], 500);
        }
    }
}
