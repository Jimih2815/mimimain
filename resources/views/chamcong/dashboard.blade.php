<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chấm công</title>
    @vite('resources/chamcong/dashboard.css')
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body>
    <div class="logout-top">
        <form method="POST" action="{{ route('chamcong.logout') }}">
            @csrf
            <button type="submit" class="logout-icon-btn" aria-label="Đăng xuất">
                <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                    <path fill="currentColor" d="M10 17v-2h4v-6h-4V7l-5 5 5 5Zm7-12v14H7v2h12V3H7v2h10Z"/>
                </svg>
            </button>
        </form>
    </div>

    <a href="{{ route('chamcong.dashboard') }}" class="logo-link">
        <img src="{{ Vite::asset('resources/chamcong/logo.png') }}" alt="Logo Công Ty" class="company-logo">
    </a>
    <h2>Xin chào, {{ $username }}!</h2>

    @if(!empty($flashMsg))
        <p style="color:green;">{{ $flashMsg }}</p>
    @endif

    <!-- @if($forgotCheckout)
        <p style="color: red; font-weight: bold;">
            Hôm qua bạn quên Checkout, báo ngay với quản lý để chấm công bù!
        </p>
    @endif -->

    @if($isCheckedIn)
        <p>Nhớ Check-Out trước khi ra về nha!</p>
        @php
            $confirmMsg = '';
            if ($diffMinutes < 3) {
                $confirmMsg = "return confirm('Bạn vừa check-in xong. Bạn có chắc chắn muốn check-out luôn?');";
            }
        @endphp
        <a class="check-btn checkout" style="width: 250px; height: 80px; display: flex; justify-content: center; align-items: center;"
           href="{{ route('chamcong.qr') }}"
           onclick="{{ $confirmMsg }}">
           Check-out
        </a>
    @else
        <p>Nhớ Check-In trước khi bắt đầu làm việc nhé!</p>
        @php
            $confirmCI = '';
            if ($justCheckedOut) {
                $confirmCI = "return confirm('Bạn vừa checkout xong. Bạn có chắc chắn muốn check-in luôn?');";
            }
        @endphp
        <a class="check-btn checkin" style="width: 250px; height: 80px; align-items: center; justify-content: center; display: flex;"
           href="{{ route('chamcong.qr') }}"
           onclick="{{ $confirmCI }}">
           Check-in
        </a>
    @endif

    @if($hasTasks)
        <div style="margin-top:20px;width: 100%; height: 80px; display: flex; justify-content: center; align-items: center;">
            <a href="{{ route('chamcong.tasks') }}"
               style="display:flex; padding:18px 40px; background:#d1a029; color:#fff; border-radius:10px; text-decoration:none; width: 250px; height: 100%; font-weight: 600; font-size: 1.3rem; align-items: center; justify-content: center;">
               Quản lý đầu việc
            </a>
        </div>
    @endif

    <div class="info-container">
        <a href="{{ route('chamcong.info') }}" class="check-btn info-btn">Thông tin</a>
    </div>

    @if($showNewTaskPopup)
        <div id="newTaskOverlay" style="position:fixed;inset:0;background:rgba(0,0,0,0.55);display:flex;align-items:center;justify-content:center;z-index:9999;">
            <div style="background:#fff;padding:25px 30px;border-radius:6px;max-width:320px;text-align:center;">
                <p style="font-weight:600;margin-bottom:18px;">
                    Bạn vừa được giao 1&nbsp;đầu&nbsp;việc mới, nhớ kiểm tra nhé!
                </p>
                <button onclick="document.getElementById('newTaskOverlay').remove();"
                        style="padding:8px 18px;border:1px solid #888;background:#f5f5f5;border-radius:4px;cursor:pointer;">Đóng</button>
            </div>
        </div>
    @endif
</body>
</html>
