<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classification;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Lấy query, trim và loại bỏ ký tự đặc biệt
        $q = trim($request->get('q',''));
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', '', $q);

        if (mb_strlen($clean) === 0) {
            // Nếu rỗng hoặc chỉ ký tự đặc biệt
            $results = collect();
        } else {
            // Phân tách theo dấu cách để tìm mở rộng
            $keywords = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);
            $query = Classification::query();
            foreach ($keywords as $word) {
                $query->where('name', 'like', "%{$word}%");
            }
            $results = $query->get();
        }

        return view('search', compact('q','results'));
    }
}
