{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title','Hồ sơ của tôi')

@section('content')
<div class="container py-5">
  {{-- Chỉnh sửa thông tin --}}
  <h2 class="mb-4">Chỉnh sửa hồ sơ</h2>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('profile.update') }}" class="profile-form">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label for="name" class="form-label">Họ & Tên</label>
      <input id="name" name="name" type="text"
             class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name', $user->name) }}" required>
      @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input id="email" name="email" type="email"
             class="form-control @error('email') is-invalid @enderror"
             value="{{ old('email', $user->email) }}" required>
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-5">
      Cập nhật thông tin
    </button>
  </form>

  {{-- Đổi mật khẩu --}}
  <h2 class="mb-4">Đổi mật khẩu</h2>

  @if(session('password_status'))
    <div class="alert alert-success">{{ session('password_status') }}</div>
  @endif

  <form method="POST" action="{{ route('password.update') }}" class="password-form">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
      <input id="current_password" name="current_password" type="password"
             class="form-control @error('current_password') is-invalid @enderror"
             required>
      @error('current_password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Mật khẩu mới</label>
      <input id="password" name="password" type="password"
             class="form-control @error('password') is-invalid @enderror"
             required>
      @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
      <input id="password_confirmation" name="password_confirmation" type="password"
             class="form-control" required>
    </div>

    <button type="submit" class="btn btn-warning w-100">
      Đổi mật khẩu
    </button>
  </form>
</div>
@endsection
