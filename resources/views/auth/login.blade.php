{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title','Đăng nhập')

@section('content')
<div class="trang-dang-nhap-cont">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">

        <h2 class="mb-4">Đăng nhập</h2>

        <form method="POST" action="{{ route('login') }}">
          @csrf

          {{-- Email --}}
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email"
                  class="form-control @error('email') is-invalid @enderror"
                  name="email" value="{{ old('email') }}" required autofocus>
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

          {{-- Remember Me --}}
          <div class="mb-3 form-check">
            <input type="checkbox"
                  class="form-check-input"
                  name="remember" id="remember"
                  {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
              Nhớ đăng nhập
            </label>
          </div>

          <button type="submit" class="btn btn-primary w-100">
            Đăng nhập
          </button>
        </form>
        <div class="dang-ky-va-quen-mat-khau">
          <p class="mt-3 text-center">
            Chưa có tài khoản?
            <a href="{{ route('register') }}">Đăng ký</a>
          </p>
          {{-- Quên mật khẩu --}}
          <div class="mb-3 text-end">
            <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
