<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HandlesWebpUpload;
use Illuminate\Http\Request;
use App\Models\HomeSectionImage;
use App\Models\Collection;

class HomeSectionImageController extends Controller
{
    use HandlesWebpUpload;

    /**
     * Hiển thị form quản lý 2 ảnh Section
     */
    public function index()
    {
        $images      = HomeSectionImage::with('collection')
                         ->orderBy('position')
                         ->get();
        $collections = Collection::pluck('name', 'id');

        return view('admin.home.images', compact('images', 'collections'));
    }

    /**
     * Xử lý cập nhật 2 ảnh Section
     */
    public function update(Request $request)
    {
        // 1) Validate
        $request->validate([
            'images.*.image'         => 'nullable|image|max:4096',
            'images.*.collection_id' => 'nullable|exists:collections,id',
        ]);

        // 2) Duyệt từng vị trí
        foreach ($request->input('images', []) as $pos => $data) {
            $img = HomeSectionImage::firstWhere('position', $pos);
            if (! $img) continue;

            $upd = [
                'collection_id' => $data['collection_id'] ?? null,
            ];

            // nếu có file mới, convert & lưu WebP
            if ($request->hasFile("images.{$pos}.image")) {
                $upd['image'] = $this->uploadAsWebp(
                    $request->file("images.{$pos}.image"),
                    'home'
                );
            }

            $img->update($upd);
        }

        return back()->with('success',
            'Đã cập nhật '.count($request->input('images', [])).' ảnh section.'
        );
    }
}
