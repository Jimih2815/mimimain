<?php

namespace App\Http\Controllers\ChamCong;

use App\Http\Controllers\Controller;
use App\Models\ChamCong\Attendance;
use App\Models\ChamCong\ChamCongUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function qr(Request $request)
    {
        $userId = (int) $request->session()->get('chamcong_user_id');
        $user = ChamCongUser::find($userId);
        if (!$user) {
            return redirect()->route('chamcong.login');
        }

        $ignoreLocation = (int) $user->ignore_location;

        return view('chamcong.qr', [
            'ignoreLocation' => $ignoreLocation,
        ]);
    }

    public function handleQr(Request $request)
    {
        $userId = (int) $request->session()->get('chamcong_user_id');
        $user = ChamCongUser::find($userId);
        if (!$user) {
            return redirect()->route('chamcong.login');
        }

        $ignoreLocation = (int) $user->ignore_location;
        $tz = config('chamcong.timezone', 'Asia/Ho_Chi_Minh');
        $now = Carbon::now($tz);

        $userLat = 0.0;
        $userLng = 0.0;

        if ($ignoreLocation === 0) {
            $request->validate([
                'lat' => ['required','numeric'],
                'lng' => ['required','numeric'],
            ]);

            $userLat = (float) $request->input('lat');
            $userLng = (float) $request->input('lng');

            $office = config('chamcong.office');
            $dist = $this->distanceInKm(
                $office['lat'],
                $office['lng'],
                $userLat,
                $userLng
            );

            if ($dist > (float) $office['radius_km']) {
                return view('chamcong.qr_error');
            }
        }

        $dateToday = $now->toDateString();
        $clientIp = $request->ip();

        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('check_in', $dateToday)
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            $checkInTime = $now->format('Y-m-d H:i:s');
            Attendance::create([
                'user_id' => $userId,
                'check_in' => $checkInTime,
                'ip_in' => $clientIp,
                'lat_in' => $userLat,
                'lng_in' => $userLng,
            ]);
            $request->session()->put('chamcong_msg', "Check-in thành công lúc {$checkInTime}");
        } else {
            $checkOutTime = $now->format('Y-m-d H:i:s');
            $attendance->update([
                'check_out' => $checkOutTime,
                'ip_out' => $clientIp,
                'lat_out' => $userLat,
                'lng_out' => $userLng,
            ]);
            $request->session()->put('chamcong_msg', "Check-out thành công lúc {$checkOutTime}");
        }

        return redirect()->route('chamcong.dashboard');
    }

    private function distanceInKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $r * $c;
    }
}
