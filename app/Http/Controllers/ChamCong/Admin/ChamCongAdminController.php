<?php

namespace App\Http\Controllers\ChamCong\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChamCong\Attendance;
use App\Models\ChamCong\ChamCongUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChamCongAdminController extends Controller
{
    public function index(Request $request)
    {
        $filterUID = (int) $request->query('filter_user_id', 0);
        $startDate = $request->query('start_date', '');
        $endDate = $request->query('end_date', '');
        $activeTab = (string) $request->query('tab', 'modul1');
        if (!preg_match('/^modul[1-5]$/', $activeTab)) {
            $activeTab = 'modul1';
        }

        $tz = config('chamcong.timezone', 'Asia/Ho_Chi_Minh');
        $now = Carbon::now($tz);

        if (empty($startDate) || empty($endDate)) {
            $startDate = $now->copy()->startOfMonth()->toDateString();
            $endDate = $now->copy()->endOfMonth()->toDateString();
        }

        $rowsPerPage = (int) $request->query('rows_per_page', 10);
        if ($rowsPerPage <= 0) {
            $rowsPerPage = 10;
        }
        $page = (int) $request->query('page', 1);
        if ($page < 1) {
            $page = 1;
        }
        $start = ($page - 1) * $rowsPerPage;

        $users = ChamCongUser::all();
        $today = $now->toDateString();
        $workingIds = Attendance::whereDate('check_in', $today)
            ->whereNull('check_out')
            ->pluck('user_id')
            ->unique()
            ->all();
        $workingMap = array_fill_keys($workingIds, true);

        $baseCount = DB::connection('chamcong')
            ->table('attendance as a')
            ->selectRaw('DATE(a.check_in) as work_date, a.user_id');

        if ($filterUID > 0) {
            $baseCount->where('a.user_id', $filterUID);
        }
        if (!empty($startDate)) {
            $baseCount->whereDate('a.check_in', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $baseCount->whereDate('a.check_in', '<=', $endDate);
        }

        $baseCount->groupBy('a.user_id', DB::raw('DATE(a.check_in)'));

        $totalDays = DB::connection('chamcong')
            ->query()
            ->fromSub($baseCount, 'sub')
            ->count();
        $totalPages = $totalDays > 0 ? (int) ceil($totalDays / $rowsPerPage) : 1;

        $groupQuery = DB::connection('chamcong')
            ->table('attendance as a')
            ->join('users as u', 'a.user_id', '=', 'u.id')
            ->selectRaw("
                u.id as user_id,
                u.username,
                DATE(a.check_in) as work_date,
                MIN(a.check_in) as earliest_in,
                MAX(a.check_out) as latest_out,
                SUBSTRING_INDEX(GROUP_CONCAT(a.id ORDER BY a.check_in ASC SEPARATOR ','), ',', 1) as earliest_id,
                SUBSTRING_INDEX(GROUP_CONCAT(a.id ORDER BY a.check_out ASC SEPARATOR ','), ',', -1) as latest_id
            ");

        if ($filterUID > 0) {
            $groupQuery->where('a.user_id', $filterUID);
        }
        if (!empty($startDate)) {
            $groupQuery->whereDate('a.check_in', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $groupQuery->whereDate('a.check_in', '<=', $endDate);
        }

        $groupedAtt = $groupQuery
            ->groupBy('u.id', DB::raw('DATE(a.check_in)'))
            ->orderByDesc('work_date')
            ->orderBy('u.id')
            ->offset($start)
            ->limit($rowsPerPage)
            ->get();

        if ($request->query('ajax') == '1') {
            return response()->json([
                'rowsHtml' => view('chamcong.admin.partials.attendance_rows', [
                    'groupedAtt' => $groupedAtt,
                ])->render(),
                'paginationHtml' => view('chamcong.admin.partials.attendance_pagination', [
                    'page' => $page,
                    'totalPages' => $totalPages,
                    'filterUID' => $filterUID,
                    'rowsPerPage' => $rowsPerPage,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                ])->render(),
                'detailUrl' => route('chamcong.admin.detail', [
                    'filter_user_id' => $filterUID,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'rows_per_page' => $rowsPerPage,
                ]),
                'page' => $page,
                'totalPages' => $totalPages,
                'rowsPerPage' => $rowsPerPage,
                'filterUID' => $filterUID,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'startDateDmy' => $this->ymdToDmy($startDate),
                'endDateDmy' => $this->ymdToDmy($endDate),
            ]);
        }

        $calcResult = [];
        $calcMonth = (int) $request->query('calc_month', 0);
        $calcYear = (int) $request->query('calc_year', 0);

        if ($calcMonth > 0 && $calcYear > 0) {
            $calcResult = $this->buildSalaryResult($calcMonth, $calcYear);
        } else {
            $calcResult = $request->session()->pull('chamcong_calc_result', []);
            $calcMonth = (int) $request->session()->pull('chamcong_calc_month', 0);
            $calcYear = (int) $request->session()->pull('chamcong_calc_year', 0);
        }

        return view('chamcong.admin.index', [
            'users' => $users,
            'workingMap' => $workingMap,
            'groupedAtt' => $groupedAtt,
            'filterUID' => $filterUID,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'startDateDmy' => $this->ymdToDmy($startDate),
            'endDateDmy' => $this->ymdToDmy($endDate),
            'rowsPerPage' => $rowsPerPage,
            'page' => $page,
            'totalPages' => $totalPages,
            'calcResult' => $calcResult,
            'calcMonth' => $calcMonth,
            'calcYear' => $calcYear,
            'activeTab' => $activeTab,
        ]);
    }

    public function toggleStatus(Request $request)
    {
        $uid = (int) $request->input('user_id');
        $tz = config('chamcong.timezone', 'Asia/Ho_Chi_Minh');
        $today = Carbon::now($tz)->toDateString();

        $row = Attendance::where('user_id', $uid)
            ->whereDate('check_in', $today)
            ->whereNull('check_out')
            ->first();

        if ($row) {
            Attendance::where('user_id', $uid)
                ->whereDate('check_in', $today)
                ->where('id', '<>', $row->id)
                ->delete();

            $checkoutTime = Carbon::now($tz)->format('Y-m-d H:i:s');
            $row->update(['check_out' => $checkoutTime]);
            $request->session()->flash('chamcong_flash_msg', "<p style='color:blue;'>Đã CHECK-OUT hộ user ID {$uid} lúc {$checkoutTime}</p>");
        } else {
            Attendance::where('user_id', $uid)
                ->whereDate('check_in', $today)
                ->delete();

            $checkinTime = Carbon::now($tz)->format('Y-m-d H:i:s');
            Attendance::create([
                'user_id' => $uid,
                'check_in' => $checkinTime,
                'ip_in' => 'ADMIN',
                'lat_in' => 0,
                'lng_in' => 0,
            ]);
            $request->session()->flash('chamcong_flash_msg', "<p style='color:blue;'>Đã CHECK-IN hộ user ID {$uid} lúc {$checkinTime}</p>");
        }

        return redirect()->route('chamcong.admin.dashboard');
    }

    public function addUser(Request $request)
    {
        $request->validate([
            'username' => ['required','string'],
            'password' => ['required','string'],
            'employee_type' => ['required','string'],
            'base_salary' => ['nullable','integer'],
            'required_hours' => ['nullable','integer'],
            'hourly_rate' => ['nullable','integer'],
        ]);

        $user = ChamCongUser::create([
            'username' => trim($request->input('username')),
            'password' => trim($request->input('password')),
            'employee_type' => $request->input('employee_type'),
            'base_salary' => (int) $request->input('base_salary', 0),
            'required_hours' => (int) $request->input('required_hours', 0),
            'hourly_rate' => (int) $request->input('hourly_rate', 0),
            'ignore_location' => 0,
        ]);

        $request->session()->flash('chamcong_flash_msg', "<p style='color:green;'>Đã thêm user mới: {$user->username}</p>");
        return redirect()->route('chamcong.admin.dashboard');
    }

    public function updateUser(Request $request)
    {
        $request->validate([
            'user_id' => ['required','integer'],
            'employee_type' => ['required','string'],
            'base_salary' => ['nullable','integer'],
            'required_hours' => ['nullable','integer'],
            'hourly_rate' => ['nullable','integer'],
        ]);

        $userId = (int) $request->input('user_id');
        $ignoreLoc = $request->has('ignore_location') ? 1 : 0;

        ChamCongUser::where('id', $userId)->update([
            'employee_type' => $request->input('employee_type'),
            'base_salary' => (int) $request->input('base_salary', 0),
            'required_hours' => (int) $request->input('required_hours', 0),
            'hourly_rate' => (int) $request->input('hourly_rate', 0),
            'ignore_location' => $ignoreLoc,
        ]);

        $request->session()->flash('chamcong_flash_msg', "<p style='color:green;'>Đã cập nhật user ID {$userId}</p>");
        return redirect()->route('chamcong.admin.dashboard');
    }

    public function deleteUser(Request $request)
    {
        $request->validate([
            'user_id' => ['required','integer'],
        ]);

        $uid = (int) $request->input('user_id');
        ChamCongUser::where('id', $uid)->delete();

        $request->session()->flash('chamcong_flash_msg', "<p style='color:red;'>Đã xóa user ID {$uid}</p>");
        return redirect()->route('chamcong.admin.dashboard');
    }

    public function addAttendance(Request $request)
    {
        $request->validate([
            'user_id' => ['required','integer'],
            'the_date' => ['required','date'],
            'in_time' => ['required','string'],
            'out_time' => ['required','string'],
        ]);

        $userId = (int) $request->input('user_id');
        $theDate = $request->input('the_date');
        $inTime = $request->input('in_time');
        $outTime = $request->input('out_time');

        $checkIn = $theDate . ' ' . $inTime . ':00';
        $checkOut = $theDate . ' ' . $outTime . ':00';

        Attendance::create([
            'user_id' => $userId,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
        ]);

        $request->session()->flash('chamcong_flash_msg', "<p style='color:green;'>Đã thêm chấm công cho user {$userId} vào ngày {$theDate}</p>");
        return redirect()->route('chamcong.admin.dashboard');
    }

    public function updateAttendance(Request $request)
    {
        $request->validate([
            'attendance_id' => ['required','integer'],
            'check_in' => ['required','string'],
            'check_out' => ['nullable','string'],
        ]);

        $attId = (int) $request->input('attendance_id');
        $checkIn = trim($request->input('check_in'));
        $checkOut = trim($request->input('check_out', ''));

        $checkIn = preg_replace('/:\\d\\d$/', ':00', str_replace('T', ' ', $checkIn));
        $checkOut = preg_replace('/:\\d\\d$/', ':00', str_replace('T', ' ', $checkOut));

        if ($checkOut === '') {
            Attendance::where('id', $attId)->update(['check_in' => $checkIn]);
        } else {
            Attendance::where('id', $attId)->update([
                'check_in' => $checkIn,
                'check_out' => $checkOut,
            ]);
        }

        $request->session()->flash('chamcong_flash_msg', "<p style='color:green;'>Đã cập nhật attendance ID {$attId}</p>");
        return redirect()->route('chamcong.admin.dashboard');
    }

    public function calculateSalary(Request $request)
    {
        $request->validate([
            'month' => ['nullable','integer','min:1','max:12'],
            'year' => ['nullable','integer','min:2000','max:2100'],
        ]);

        $tz = config('chamcong.timezone', 'Asia/Ho_Chi_Minh');
        $now = Carbon::now($tz);

        $monthInput = $request->input('month');
        $yearInput = $request->input('year');

        $month = (int) ($monthInput ?: $now->format('m'));
        $year = (int) ($yearInput ?: $now->format('Y'));

        return redirect()->route('chamcong.admin.dashboard', [
            'tab' => 'modul3',
            'calc_month' => $month,
            'calc_year' => $year,
        ], 302)->withFragment('bang-luong');
    }

    private function buildSalaryResult(int $month, int $year): array
    {
        $users = ChamCongUser::all();
        $totalResult = [];

        foreach ($users as $u) {
            $uid = $u->id;

            $rowMins = Attendance::where('user_id', $uid)
                ->whereMonth('check_in', $month)
                ->whereYear('check_in', $year)
                ->whereNotNull('check_out')
                ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, check_in, check_out)) AS total_mins')
                ->value('total_mins');
            $totalMins = (int) ($rowMins ?? 0);

            $actualHours = $totalMins / 60.0;
            $salary = 0.0;

            if ($u->employee_type === 'chinh_thuc') {
                $base = (float) $u->base_salary;
                $reqH = (float) $u->required_hours;
                if ($reqH > 0) {
                    $salary = $base * ($actualHours / $reqH);
                }
            } else {
                $rate = (float) $u->hourly_rate;
                $salary = $rate * $actualHours;
            }

            $totalResult[] = [
                'user_id' => $u->id,
                'username' => $u->username,
                'employee_type' => $u->employee_type,
                'total_mins' => $totalMins,
                'actual_hours' => $actualHours,
                'salary' => $salary,
            ];
        }

        usort($totalResult, function ($a, $b) {
            return ($b['salary'] ?? 0) <=> ($a['salary'] ?? 0);
        });

        return $totalResult;
    }

    public function deleteDayAttendance(Request $request)
    {
        $request->validate([
            'user_id' => ['required','integer'],
            'the_date' => ['required','date'],
        ]);

        $userId = (int) $request->input('user_id');
        $theDate = $request->input('the_date');

        Attendance::where('user_id', $userId)
            ->whereDate('check_in', $theDate)
            ->delete();

        $request->session()->flash('chamcong_flash_msg', "<p style='color:red;'>Đã xóa toàn bộ chấm công ngày {$theDate} của user_id={$userId}</p>");
        return redirect()->route('chamcong.admin.dashboard', [], 302)->withFragment('table-chamcong');
    }

    public function updateEarliestLatest(Request $request)
    {
        $request->validate([
            'earliest_id' => ['required','integer'],
            'latest_id' => ['required','integer'],
            'the_date' => ['required','date'],
            'earliest_in' => ['required','string'],
            'latest_out' => ['required','string'],
        ]);

        $earliestId = (int) $request->input('earliest_id');
        $latestId = (int) $request->input('latest_id');
        $theDate = $request->input('the_date');
        $earliestIn = trim($request->input('earliest_in'));
        $latestOut = trim($request->input('latest_out'));

        $checkIn = $theDate . ' ' . $earliestIn . ':00';
        $checkOut = $theDate . ' ' . $latestOut . ':00';

        if ($earliestId === $latestId) {
            Attendance::where('id', $earliestId)->update([
                'check_in' => $checkIn,
                'check_out' => $checkOut,
            ]);
        } else {
            Attendance::where('id', $earliestId)->update([
                'check_in' => $checkIn,
                'check_out' => $checkOut,
            ]);
            Attendance::where('id', $latestId)->delete();
        }

        $request->session()->flash('chamcong_flash_msg', "<p style='color:green;'>Đã cập nhật chấm công cho ngày {$theDate}</p>");
        return redirect()->route('chamcong.admin.dashboard', [], 302)->withFragment('table-chamcong');
    }

    private function toHourMinute(?string $ymdHis): string
    {
        if (!$ymdHis) {
            return '';
        }
        $parts = explode(' ', $ymdHis);
        if (count($parts) < 2) {
            return '';
        }
        return substr($parts[1], 0, 5);
    }

    private function ymdToDmy(?string $ymd): string
    {
        if (!$ymd) {
            return '';
        }
        $arr = explode('-', $ymd);
        if (count($arr) < 3) {
            return $ymd;
        }
        return $arr[2] . '/' . $arr[1] . '/' . $arr[0];
    }
}
