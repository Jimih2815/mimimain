<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HelpRequest;

class HelpRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // nếu bạn có middleware kiểm tra role admin
        // $this->middleware('can:admin');
    }

    /**
     * POST /admin/help-requests/{helpRequest}/update
     */
    public function update(Request $request, HelpRequest $helpRequest)
    {
        $data = $request->validate([
            'response' => 'nullable|string',
            'status'   => 'required|in:Đã tiếp nhận,Đang xử lý,Hoàn thành',
        ]);

        $helpRequest->update($data);

        return back()->with('success', 'Cập nhật yêu cầu thành công!');
    }
}
