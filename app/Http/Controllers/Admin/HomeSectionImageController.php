<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeSectionImage;
use App\Models\Collection;

class HomeSectionImageController extends Controller
{
    public function edit()
    {
        $images      = HomeSectionImage::orderBy('position')->get();
        $collections = Collection::pluck('name','id');
        return view('admin.home.images', compact('images','collections'));
    }

    public function update(Request $r)
    {
        $r->validate([
          'images.*.image'         => 'nullable|image|max:4096',
          'images.*.collection_id' => 'nullable|exists:collections,id',
        ]);

        foreach($r->input('images', []) as $pos => $data) {
            $img = HomeSectionImage::firstWhere('position', $pos);
            if (!$img) continue;

            $upd = ['collection_id'=> $data['collection_id'] ?? null];

            if ($r->hasFile("images.$pos.image")) {
                $upd['image'] = $r->file("images.$pos.image")
                                  ->store('home','public');
            }

            $img->update($upd);
        }

        return back()->with('success','Đã cập nhật 2 ảnh section.');
    }
}
