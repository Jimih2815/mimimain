<?php

namespace App\Http\Controllers\ChamCong\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChamCong\Attendance;
use App\Models\ChamCong\ChamCongUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailController extends Controller
{
    public function index(Request $request)
    {
        $filterUID = (int) $request->query('filter_user_id', 0);
        $startDate = $request->query('start_date', '');
        $endDate = $request->query('end_date', '');
        $rowsPerPage = (int) $request->query('rows_per_page', 10);
        if ($rowsPerPage <= 0) {
            $rowsPerPage = 10;
        }
        $page = (int) $request->query('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        if (empty($startDate) || empty($endDate)) {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }

        $startDateDmy = $this->ymdToDmy($startDate);
        $endDateDmy = $this->ymdToDmy($endDate);

        $allUsers = ChamCongUser::all();

        $countQuery = DB::connection('chamcong')
            ->table('attendance as a')
            ->selectRaw('DATE(a.check_in) as work_date, a.user_id');

        if ($filterUID > 0) {
            $countQuery->where('a.user_id', $filterUID);
        }
        if (!empty($startDate)) {
            $countQuery->whereDate('a.check_in', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $countQuery->whereDate('a.check_in', '<=', $endDate);
        }

        $countQuery->groupBy('a.user_id', DB::raw('DATE(a.check_in)'));
        $totalDays = DB::connection('chamcong')->query()->fromSub($countQuery, 'sub')->count();
        $totalPages = $totalDays > 0 ? (int) ceil($totalDays / $rowsPerPage) : 1;

        $allRowsQuery = DB::connection('chamcong')
            ->table('attendance as a')
            ->join('users as u', 'a.user_id', '=', 'u.id')
            ->selectRaw('u.id as user_id, u.username, DATE(a.check_in) as work_date, MIN(a.check_in) as earliest_in, MAX(a.check_out) as latest_out');

        if ($filterUID > 0) {
            $allRowsQuery->where('a.user_id', $filterUID);
        }
        if (!empty($startDate)) {
            $allRowsQuery->whereDate('a.check_in', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $allRowsQuery->whereDate('a.check_in', '<=', $endDate);
        }

        $allRows = $allRowsQuery
            ->groupBy('u.id', DB::raw('DATE(a.check_in)'))
            ->orderByDesc('work_date')
            ->orderBy('u.id')
            ->get();

        $sumHours = 0;
        $sumSalary = 0;
        foreach ($allRows as $r) {
            $earliestTS = strtotime($r->earliest_in);
            $latestTS = strtotime($r->latest_out);
            $dailyHours = 0;
            if ($earliestTS && $latestTS && $latestTS > $earliestTS) {
                $diffSec = $latestTS - $earliestTS;
                $dailyHours = round($diffSec / 3600, 2);
            }
            $sumHours += $dailyHours;

            $daySalary = $this->getUserSalaryPerDay($r->user_id, $r->earliest_in, $r->latest_out);
            $sumSalary += $daySalary;
        }

        $start = ($page - 1) * $rowsPerPage;
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

        $sumHoursDisplay = round($sumHours, 2);
        $sumSalaryDisplay = number_format($sumSalary, 0, ',', '.');

        if ($request->ajax() || $request->query('ajax') == '1') {
            return response()->json([
                'rowsHtml' => view('chamcong.admin.partials.detail_rows', [
                    'groupedAtt' => $groupedAtt,
                ])->render(),
                'page' => $page,
                'totalPages' => $totalPages,
                'rowsPerPage' => $rowsPerPage,
                'filterUID' => $filterUID,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'startDateDmy' => $startDateDmy,
                'endDateDmy' => $endDateDmy,
                'sumHours' => $sumHours,
                'sumHoursDisplay' => $sumHoursDisplay,
                'sumSalary' => $sumSalary,
                'sumSalaryFormatted' => $sumSalaryDisplay,
            ]);
        }

        return view('chamcong.admin.detail', [
            'allUsers' => $allUsers,
            'groupedAtt' => $groupedAtt,
            'filterUID' => $filterUID,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'startDateDmy' => $startDateDmy,
            'endDateDmy' => $endDateDmy,
            'rowsPerPage' => $rowsPerPage,
            'page' => $page,
            'totalPages' => $totalPages,
            'sumHours' => $sumHours,
            'sumSalary' => $sumSalary,
        ]);
    }

    private function getUserSalaryPerDay(int $uid, ?string $earliestIn, ?string $latestOut): float
    {
        $user = ChamCongUser::find($uid);
        if (!$user) {
            return 0;
        }

        $earliestTS = strtotime($earliestIn ?? '');
        $latestTS = strtotime($latestOut ?? '');
        if (!$earliestTS || !$latestTS || $latestTS <= $earliestTS) {
            return 0;
        }

        $hours = ($latestTS - $earliestTS) / 3600.0;
        if ($user->employee_type === 'chinh_thuc') {
            $base = (float) $user->base_salary;
            $reqH = (float) $user->required_hours;
            if ($reqH > 0) {
                return $base * ($hours / $reqH);
            }
            return 0;
        }

        $rate = (float) $user->hourly_rate;
        return $rate * $hours;
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
