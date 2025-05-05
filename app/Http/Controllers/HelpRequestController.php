<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HelpRequest;

class HelpRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Hiển thị form
    public function create()
    {
        return view('help');
    }

    // Xử lý lưu yêu cầu
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        HelpRequest::create([
            'user_id' => auth()->id(),
            'name'    => $data['name'],
            'phone'   => $data['phone'],
            'message' => $data['message'],
        ]);

        return redirect()
            ->route('help.create')
            ->with('success', 'Yêu cầu của bạn đã được gửi. Chúng tôi sẽ sớm liên hệ!');
    }
    
}
