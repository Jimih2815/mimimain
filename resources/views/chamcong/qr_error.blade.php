<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lỗi Chấm Công</title>
    @vite('resources/chamcong/qr.css')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="error-message">
        <h2>Không thể chấm công!</h2>
        <p>Bạn đang không ở văn phòng!</p>
        <a href="{{ route('chamcong.dashboard') }}" class="back-btn">Quay lại Trang Chính</a>
    </div>
</body>
</html>
