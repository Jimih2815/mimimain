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

        $tz = config('chamcong.timezone', 'Asia/Ho_Chi_Minh');
        $now = Carbon::now($tz);

        $selectedMonth = (string) $request->query('month', $now->format('Y-m'));
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $selectedMonth)) {
            $selectedMonth = $now->format('Y-m');
        }
        [$filterYear, $filterMonth] = array_map('intval', explode('-', $selectedMonth));

        $monthOptions = [];
        for ($i = 0; $i < 6; $i++) {
            $d = $now->copy()->subMonths($i);
            $monthOptions[] = [
                'value' => $d->format('Y-m'),
                'label' => $d->format('m/Y'),
            ];
        }

        $rowsPerPage = (int) $request->query('rows_per_page', 5);
        if (!in_array($rowsPerPage, [5, 10, 20, 30], true)) {
            $rowsPerPage = 5;
        }
        $page = max(1, (int) $request->query('page', 1));
        $startFrom = ($page - 1) * $rowsPerPage;

        $totalRowsQuery = Attendance::where('user_id', $userId);
        if ($filterYear > 0 && $filterMonth > 0) {
            $totalRowsQuery->whereYear('check_in', $filterYear)
                ->whereMonth('check_in', $filterMonth);
        }
        $totalRows = $totalRowsQuery
            ->selectRaw('COUNT(DISTINCT DATE(check_in)) AS total_days')
            ->value('total_days');
        $totalRows = (int) ($totalRows ?? 0);
        $totalPages = $totalRows > 0 ? (int) ceil($totalRows / $rowsPerPage) : 1;

        $groupedQuery = Attendance::where('user_id', $userId)
            ->selectRaw('DATE(check_in) AS work_date, MIN(check_in) AS earliest_in, MAX(check_out) AS latest_out')
            ->groupBy(DB::raw('DATE(check_in)'));
        if ($filterYear > 0 && $filterMonth > 0) {
            $groupedQuery->whereYear('check_in', $filterYear)
                ->whereMonth('check_in', $filterMonth);
        }
        $groupedAtt = $groupedQuery
            ->orderByDesc('work_date')
            ->offset($startFrom)
            ->limit($rowsPerPage)
            ->get();

        $calendarDays = [];
        if ($filterYear > 0 && $filterMonth > 0) {
            $calendarRows = Attendance::where('user_id', $userId)
                ->whereYear('check_in', $filterYear)
                ->whereMonth('check_in', $filterMonth)
                ->selectRaw('DATE(check_in) AS work_date, MAX(check_in IS NOT NULL) AS has_in, MAX(check_out IS NOT NULL) AS has_out')
                ->groupBy(DB::raw('DATE(check_in)'))
                ->get();

            $calendarDays = $calendarRows->map(function ($r) {
                $day = (int) substr((string) $r->work_date, 8, 2);
                $hasIn = (int) $r->has_in > 0;
                $hasOut = (int) $r->has_out > 0;
                $status = ($hasIn && $hasOut) ? 'complete' : 'incomplete';
                return [
                    'day' => $day,
                    'status' => $status,
                ];
            })->values();
        }

        $totalMinsQuery = Attendance::where('user_id', $userId);
        if ($filterYear > 0 && $filterMonth > 0) {
            $totalMinsQuery->whereYear('check_in', $filterYear)
                ->whereMonth('check_in', $filterMonth);
        }
        $totalMins = $totalMinsQuery
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

        if ($request->query('ajax') == '1') {
            $rows = $groupedAtt->map(function ($g) {
                $dayString = $g->work_date;
                $earliestHM = $g->earliest_in ? substr(explode(' ', $g->earliest_in)[1] ?? '', 0, 5) : '';
                $latestHM = $g->latest_out ? substr(explode(' ', $g->latest_out)[1] ?? '', 0, 5) : '';
                $dmy = $dayString ? implode('/', array_reverse(explode('-', $dayString))) : '';
                return [
                    'date' => $dmy,
                    'check_in' => $earliestHM,
                    'check_out' => $latestHM,
                ];
            })->values();

            return response()->json([
                'rows' => $rows,
                'page' => $page,
                'totalPages' => $totalPages,
                'rowsPerPage' => $rowsPerPage,
                'month' => $selectedMonth,
                'actualHours' => round($actualHoursThisMonth, 2),
                'currentSalary' => number_format($currentSalary),
                'calendar' => $calendarDays,
            ]);
        }

        $passMsg = $request->session()->pull('chamcong_pass_msg');

        return view('chamcong.info', [
            'username' => $username,
            'groupedAtt' => $groupedAtt,
            'page' => $page,
            'totalPages' => $totalPages,
            'rowsPerPage' => $rowsPerPage,
            'monthOptions' => $monthOptions,
            'selectedMonth' => $selectedMonth,
            'calendarDays' => $calendarDays,
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
