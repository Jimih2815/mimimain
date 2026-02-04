<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;            // nếu bạn dùng queued jobs
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Jenssegers\Agent\Agent;                              // ← thêm dòng này

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function isPhone(): bool
    {
        $agent = new Agent();
        return $agent->isMobile() && ! $agent->isTablet();
    }

    /**
     * Trả về bản mobile nếu đang mobile & có view-mobile,
     * ngược lại trả về bản desktop.
     *
     * @param  string  $name  Tên view (ví dụ 'home', 'profile.edit', ...)
     * @param  array   $data  Dữ liệu truyền view
     */
    protected function renderView(string $name, array $data = [])
    {
        $mobile = "{$name}-mobile";

        // nếu điện thoại và tồn tại file view-mobile.blade.php
        if ($this->isPhone() && view()->exists($mobile)) {
            return view($mobile, $data);
        }

        // fallback desktop
        return view($name, $data);
    }
}
