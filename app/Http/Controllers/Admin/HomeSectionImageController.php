<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeSectionImage;
use App\Models\Collection;

class HomeSectionImageController extends Controller
{
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
        // 1) Validate đầu vào
        $request->validate([
            'images.*.image'         => 'nullable|image|max:4096',
            'images.*.collection_id' => 'nullable|exists:collections,id',
        ]);

        // 2) Duyệt từng vị trí và lưu file nếu có
        foreach ($request->input('images', []) as $pos => $data) {
            /** @var HomeSectionImage $img */
            $img = HomeSectionImage::firstWhere('position', $pos);
            if (! $img) {
                continue;
            }

            // Luôn cập nhật lại collection_id
            $upd = [
              'collection_id' => $data['collection_id'] ?? null,
            ];

            // Nếu có upload file mới cho vị trí này...
            if ($request->hasFile("images.$pos.image")) {
                // Lưu vào storage/app/public/home, trả về "home/xxx.jpg"
                $path = $request
                    ->file("images.$pos.image")
                    ->store('home', 'public');
                $upd['image'] = $path;
            }

            // Cập nhật DB
            $img->update($upd);
        }

        return back()
             ->with('success',
                    'Đã cập nhật '.
                    count($request->input('images', [])).
                    ' ảnh section.');
    }
}
