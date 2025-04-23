{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title','Đăng ký')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">

      <h2 class="mb-4">Tạo tài khoản mới</h2>

      <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div class="mb-3">
          <label for="name" class="form-label">Họ & Tên</label>
          <input id="name" type="text"
                 class="form-control @error('name') is-invalid @enderror"
                 name="name" value="{{ old('name') }}" required autofocus>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input id="email" type="email"
                 class="form-control @error('email') is-invalid @enderror"
                 name="email" value="{{ old('email') }}" required>
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
          <label for="password" class="form-label">Mật khẩu</label>
          <input id="password" type="password"
                 class="form-control @error('password') is-invalid @enderror"
                 name="password" required>
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-3">
          <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
          <input id="password_confirmation" type="password"
                 class="form-control"
                 name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">
          Đăng ký
        </button>
      </form>

      <p class="mt-3 text-center">
        Đã có tài khoản?
        <a href="{{ route('login') }}">Đăng nhập</a>
      </p>

    </div>
  </div>
</div>
@endsection
