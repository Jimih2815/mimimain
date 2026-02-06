<?php

namespace App\Http\Controllers\ChamCong;

use App\Http\Controllers\Controller;
use App\Models\ChamCong\Attendance;
use App\Models\ChamCong\TaskAssignee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = (int) $request->session()->get('chamcong_user_id');
        $username = $request->session()->get('chamcong_username', '');

        $tz = config('chamcong.timezone', 'Asia/Ho_Chi_Minh');
        $now = Carbon::now($tz);

        $showNewTaskPopup = TaskAssignee::where('user_id', $userId)
            ->where('seen', 0)
            ->exists();

        $hasTasks = TaskAssignee::where('user_id', $userId)->exists();

        $dateToday = $now->toDateString();
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('check_in', $dateToday)
            ->whereNull('check_out')
            ->first();

        $isCheckedIn = $attendance !== null;
        $diffMinutes = 9999;
        if ($attendance) {
            $checkInTime = Carbon::parse($attendance->check_in, $tz);
            $diffMinutes = $checkInTime->diffInMinutes($now);
        }

        $lastAtt = Attendance::where('user_id', $userId)
            ->orderByDesc('id')
            ->first();

        $justCheckedOut = false;
        if ($lastAtt && !empty($lastAtt->check_out)) {
            $coTime = Carbon::parse($lastAtt->check_out, $tz);
            $justCheckedOut = $coTime->diffInMinutes($now) < 1;
        }

        $yesterday = $now->copy()->subDay()->toDateString();
        $forgotCheckout = Attendance::where('user_id', $userId)
            ->whereDate('check_in', $yesterday)
            ->whereNull('check_out')
            ->exists();

        $flashMsg = $request->session()->pull('chamcong_msg');

        return view('chamcong.dashboard', [
            'username' => $username,
            'isCheckedIn' => $isCheckedIn,
            'diffMinutes' => $diffMinutes,
            'justCheckedOut' => $justCheckedOut,
            'forgotCheckout' => $forgotCheckout,
            'showNewTaskPopup' => $showNewTaskPopup,
            'hasTasks' => $hasTasks,
            'flashMsg' => $flashMsg,
        ]);
    }
}
