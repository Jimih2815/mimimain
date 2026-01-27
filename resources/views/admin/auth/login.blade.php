<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .nut-dang-nhap {
            border-radius: 50px;
    background-color: #4ab3af;
    color: white;
    font-size: 1.1rem;
    border-color: #4ab3af;
    }
    .nut-dang-nhap:hover {
        background-color: #65b6b3ff
    }
  </style>
</head>
<body class="bg-light">

  <div class="container py-5" style="max-width: 460px;">
    <div class="text-center mb-4">
      <img src="https://tiemhoamimi.com/image/mimi-logo.webp" alt="MiMi" style="height:70px;object-fit:contain;">
      <h4 class="mt-3">Đăng nhập Admin</h4>
    </div>

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        @foreach($errors->all() as $e)
          <div>- {{ $e }}</div>
        @endforeach
      </div>
    @endif

    <div class="card shadow-sm">
      <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.login.submit') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input name="username" class="form-control" autocomplete="username" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" autocomplete="current-password" required>
          </div>

          <button class="nut-dang-nhap w-100">Đăng nhập</button>
        </form>
      </div>
    </div>

    <div class="text-center text-muted mt-3" style="font-size: 13px;">
      Tip: đừng share link admin cho ai nha 🤫
    </div>
  </div>

</body>
</html>
