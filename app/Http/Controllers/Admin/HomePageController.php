<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomePage;
use App\Models\Collection;

class HomePageController extends Controller
{
    public function edit()
    {
        $home        = HomePage::first();
        $collections = Collection::pluck('name','id');
        return view('admin.home.edit', compact('home','collections'));
    }

    public function update(Request $r)
    {
        $data = $r->validate([
            'banner_image'                     => 'nullable|image|max:4096',
            'about_title'                      => 'nullable|string|max:255',
            'about_text'                       => 'nullable|string',
            'show_button'                      => 'sometimes',
            'button_collection_id'             => 'nullable|exists:collections,id',
            'button_text'                      => 'nullable|string|max:50',
            'pre_banner_title'                 => 'nullable|string|max:100', 
            'pre_banner_button_text'           => 'nullable|string|max:50',
            'pre_banner_button_collection_id'  => 'nullable|exists:collections,id',
        ]);

        // Xử lý checkbox
        $data['show_button'] = $r->has('show_button');

        // Xử lý banner
        if ($r->hasFile('banner_image')) {
            $data['banner_image'] = $r->file('banner_image')->store('home','public');
        }

        HomePage::first()->update($data);

        return redirect()
            ->route('admin.home.edit')
            ->with('success','Cập nhật Home Page thành công');
    }
}
