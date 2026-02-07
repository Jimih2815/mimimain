<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>MiMi-Chấm Công</title>
    @vite('resources/chamcong/index.css')
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>
<body>
    <a href="{{ route('chamcong.dashboard') }}" class="logo-link">
        <img src="{{ asset('image/mimi-logo.webp') }}" alt="Logo Công Ty" class="company-logo">
    </a>
    <div class="index-body">
        <h2>Đăng nhập</h2>
        @if(session('error'))
            <p style="color:red;">{{ session('error') }}</p>
        @endif
        <form method="POST" action="{{ route('chamcong.login.submit') }}">
            @csrf
            <label for="username">Tài khoản: </label>
            <input type="text" name="username" required><br><br>

            <label for="password">Mật khẩu: </label>
            <input type="password" name="password" required><br><br>

            <label style="margin: 0; height: 30px; display: flex; justify-content: flex-start;">
                <div style="height: 100%; display: flex; justify-content: center; padding: 5px; align-items: center;">
                    <input type="checkbox" name="remember_me" value="1">
                </div>
                <p style="align-items: center; justify-content: center; display: flex; padding: 5px;">Ghi nhớ đăng nhập</p>
            </label>
            <br><br>
            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>
