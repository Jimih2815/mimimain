<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>MiMi-Chấm công</title>
    @vite('resources/chamcong/qr.css')
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body>
@if($ignoreLocation === 0)
    <h3>Đang xác định vị trí của bạn...</h3>
    <p>Vui lòng cho phép truy cập vị trí (GPS) để chấm công.</p>
    <form id="geoForm" method="POST" action="{{ route('chamcong.qr.handle') }}">
        @csrf
        <input type="hidden" name="lat" id="lat">
        <input type="hidden" name="lng" id="lng">
    </form>
    <script>
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            function success(pos) {
                document.getElementById('lat').value = pos.coords.latitude;
                document.getElementById('lng').value = pos.coords.longitude;
                document.getElementById('geoForm').submit();
            },
            function error() {
                alert("Bạn đã từ chối hoặc không lấy được vị trí.\nKhông thể check-in/out!");
                window.location.href = "{{ route('chamcong.dashboard') }}";
            },
            { enableHighAccuracy:true, timeout:15000, maximumAge:0 }
        );
    } else {
        alert("Trình duyệt không hỗ trợ geolocation!");
        window.location.href = "{{ route('chamcong.dashboard') }}";
    }
    </script>
@else
    <form id="noLocationForm" method="POST" action="{{ route('chamcong.qr.handle') }}">
        @csrf
        <input type="hidden" name="lat" value="0">
        <input type="hidden" name="lng" value="0">
    </form>
    <script>
        document.getElementById('noLocationForm').submit();
    </script>
@endif
</body>
</html>
