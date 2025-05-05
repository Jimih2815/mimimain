<?php

namespace App\Http\Controllers;

use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $posts = News::latest()->paginate(10);
        return view('news.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = News::where('slug', $slug)->firstOrFail();
        return view('news.show', compact('post'));
    }
}
