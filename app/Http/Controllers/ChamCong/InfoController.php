<?php

namespace App\Http\Controllers\ChamCong;

use App\Http\Controllers\Controller;
use App\Models\ChamCong\Attendance;
use App\Models\ChamCong\ChamCongUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfoController extends Controller
{
    public function index(Request $request)
    {
        $userId = (int) $request->session()->get('chamcong_user_id');
        $username = $request->session()->get('chamcong_username', '');

        $rowsPerPage = 5;
        $page = max(1, (int) $request->query('page', 1));
        $startFrom = ($page - 1) * $rowsPerPage;

        $totalRows = Attendance::where('user_id', $userId)
            ->selectRaw('COUNT(DISTINCT DATE(check_in)) AS total_days')
            ->value('total_days');
        $totalRows = (int) ($totalRows ?? 0);
        $totalPages = $totalRows > 0 ? (int) ceil($totalRows / $rowsPerPage) : 1;

        $groupedAtt = Attendance::where('user_id', $userId)
            ->selectRaw('DATE(check_in) AS work_date, MIN(check_in) AS earliest_in, MAX(check_out) AS latest_out')
            ->groupBy(DB::raw('DATE(check_in)'))
            ->orderByDesc('work_date')
            ->offset($startFrom)
            ->limit($rowsPerPage)
            ->get();

        $tz = config('chamcong.timezone', 'Asia/Ho_Chi_Minh');
        $now = Carbon::now($tz);

        $totalMins = Attendance::where('user_id', $userId)
            ->whereMonth('check_in', $now->month)
            ->whereYear('check_in', $now->year)
            ->whereNotNull('check_out')
            ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, check_in, check_out)) AS total_mins')
            ->value('total_mins');
        $totalMins = (int) ($totalMins ?? 0);
        $actualHoursThisMonth = $totalMins / 60.0;

        $user = ChamCongUser::find($userId);
        $currentSalary = 0;
        if ($user) {
            if ($user->employee_type === 'chinh_thuc') {
                $hoursRequired = (float) $user->required_hours;
                $baseSalary = (float) $user->base_salary;
                if ($hoursRequired > 0) {
                    if ($actualHoursThisMonth >= $hoursRequired) {
                        $currentSalary = $baseSalary;
                    } else {
                        $currentSalary = $baseSalary * ($actualHoursThisMonth / $hoursRequired);
                    }
                }
            } else {
                $currentSalary = (float) $user->hourly_rate * $actualHoursThisMonth;
            }
        }

        $passMsg = $request->session()->pull('chamcong_pass_msg');

        return view('chamcong.info', [
            'username' => $username,
            'groupedAtt' => $groupedAtt,
            'page' => $page,
            'totalPages' => $totalPages,
            'actualHoursThisMonth' => $actualHoursThisMonth,
            'currentSalary' => $currentSalary,
            'passMsg' => $passMsg,
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => ['required','string'],
            'new_password' => ['required','string'],
            'confirm_new_password' => ['required','string'],
        ]);

        $userId = (int) $request->session()->get('chamcong_user_id');
        $user = ChamCongUser::find($userId);
        if (!$user) {
            return redirect()->route('chamcong.login');
        }

        $old = trim($request->input('old_password'));
        $new = trim($request->input('new_password'));
        $confirm = trim($request->input('confirm_new_password'));

        $msg = '';
        if ($user->password !== $old) {
            $msg = 'Mật khẩu cũ không đúng!';
        } else {
            $pattern = '/^[a-zA-Z0-9!@#$%^&*()_\\-+=]+$/';
            if (!preg_match($pattern, $new)) {
                $msg = 'Mật khẩu chỉ được chứa chữ cái (không dấu), chữ số, và các ký tự !@#$%^&*()_-+=';
            } elseif ($new === $old) {
                $msg = 'Mật khẩu mới không được trùng với mật khẩu cũ!';
            } elseif ($new !== $confirm) {
                $msg = 'Mật khẩu mới và xác nhận không trùng khớp!';
            } else {
                $user->password = $new;
                $user->save();
                $msg = 'Đổi mật khẩu thành công!';
            }
        }

        $request->session()->put('chamcong_pass_msg', $msg);
        return redirect()->route('chamcong.info');
    }
}
