<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin cá nhân</title>
    @vite('resources/chamcong/info.css')
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <style>
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        .success { color: green; }
        .error { color: red; }
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .modal.show { display: block; }
        .modal-content { background-color: #fff; margin: 10% auto; padding: 20px; width: 90%; max-width: 400px; border-radius: 5px; position: relative; }
        .close { position: absolute; right: 15px; top: 10px; cursor: pointer; font-size: 24px; }
        .container { max-width: 1200px; margin: auto; padding: 1rem; }
    </style>
</head>
<body>
<div class="container">
    <h2>Thông tin của bạn: {{ $username }}</h2>

    <h3>Lịch sử chấm công</h3>
    <table>
        <tr>
            <th>Ngày</th>
            <th>Giờ Check-In</th>
            <th>Giờ Check-Out</th>
        </tr>
        @foreach($groupedAtt as $g)
            @php
                $dayString = $g->work_date;
                $earliestHM = $g->earliest_in ? substr(explode(' ', $g->earliest_in)[1] ?? '', 0, 5) : '';
                $latestHM = $g->latest_out ? substr(explode(' ', $g->latest_out)[1] ?? '', 0, 5) : '';
                $dmy = $dayString ? implode('/', array_reverse(explode('-', $dayString))) : '';
            @endphp
            <tr>
                <td>{{ $dmy }}</td>
                <td>{{ $earliestHM }}</td>
                <td>{{ $latestHM }}</td>
            </tr>
        @endforeach
    </table>

    @php
        $startPage = $page - 1;
        $endPage = $page + 1;
        if ($page <= 2) { $startPage = 1; $endPage = 3; }
        if ($page >= $totalPages - 1) { $startPage = $totalPages - 2; $endPage = $totalPages; }
        if ($startPage < 1) $startPage = 1;
        if ($endPage > $totalPages) $endPage = $totalPages;
    @endphp

    <div class="pagination">
        @for($i = $startPage; $i <= $endPage; $i++)
            <a href="{{ route('chamcong.info', ['page' => $i]) }}" class="{{ $i == $page ? 'active' : '' }}">
                {{ $i }}
            </a>
        @endfor
    </div>

    <div class="summary">
        <p class="total-hours">
            <strong>Tổng giờ làm việc:</strong>
            <span>{{ round($actualHoursThisMonth, 2) }} giờ</span>
        </p>
        <p class="current-salary">
            <strong>Lương hiện tại:</strong>
            <span>{{ number_format($currentSalary) }} VNĐ</span>
        </p>
    </div>

    <button class="open-password-modal">Đổi mật khẩu</button>

    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Đổi mật khẩu</h3>

            @if(!empty($passMsg))
                <p style="color: {{ $passMsg === 'Đổi mật khẩu thành công!' ? 'green' : 'red' }};">
                    {{ $passMsg }}
                </p>
            @endif

            <form method="POST" action="{{ route('chamcong.info.password') }}">
                @csrf
                <label for="old_password">Mật khẩu cũ:</label>
                <input type="password" name="old_password" required>

                <label for="new_password">Mật khẩu mới:</label>
                <input type="password" name="new_password" required>

                <label for="confirm_new_password">Xác nhận mật khẩu mới:</label>
                <input type="password" name="confirm_new_password" required>

                <button type="submit">Xác nhận đổi mật khẩu</button>
            </form>
        </div>
    </div>

    <a href="{{ route('chamcong.dashboard') }}" class="back-link">Quay lại trang chính</a>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("passwordModal");
    const btn = document.querySelector(".open-password-modal");
    const closeBtn = document.querySelector(".close");

    if (btn) {
        btn.onclick = function() { modal.classList.add("show"); }
    }
    if (closeBtn) {
        closeBtn.onclick = function() { modal.classList.remove("show"); }
    }
    window.onclick = function(event) {
        if (event.target === modal) { modal.classList.remove("show"); }
    }
    @if(!empty($passMsg) && $passMsg !== "Đổi mật khẩu thành công!")
        modal.classList.add("show");
    @endif
});
</script>
</body>
</html>
