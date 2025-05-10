{{-- resources/views/profile/partials/info-mobile.blade.php --}}
<style>
 
</style>
<div class="card mb-3">
  <div class="card-body">
    <h5 class="card-title">Thông tin tài khoản</h5>

    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}">
      @csrf
      @method('PUT')

      <div class="mb-2">
        <label class="form-label small" for="name">Họ & Tên</label>
        <input id="name" name="name" type="text"
               class="form-control form-control-sm @error('name') is-invalid @enderror"
               value="{{ old('name', $user->name) }}" required>
        @error('name')
          <div class="invalid-feedback small">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label small" for="email">Email</label>
        <input id="email" name="email" type="email"
               class="form-control form-control-sm @error('email') is-invalid @enderror"
               value="{{ old('email', $user->email) }}" required>
        @error('email')
          <div class="invalid-feedback small">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit" class="btn-mimi nut-xanh w-100">Cập nhật</button>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Đổi mật khẩu</h5>

    @if(session('password_status'))
      <div class="alert alert-success">{{ session('password_status') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.password.update') }}">
      @csrf
      @method('PUT')

      <div class="mb-2">
        <label class="form-label small" for="current_password">Mật khẩu hiện tại</label>
        <input id="current_password" name="current_password" type="password"
               class="form-control form-control-sm @error('current_password') is-invalid @enderror"
               required>
        @error('current_password')
          <div class="invalid-feedback small">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-2">
        <label class="form-label small" for="password">Mật khẩu mới</label>
        <input id="password" name="password" type="password"
               class="form-control form-control-sm @error('password') is-invalid @enderror"
               required>
        @error('password')
          <div class="invalid-feedback small">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label small" for="password_confirmation">Xác nhận mật khẩu</label>
        <input id="password_confirmation" name="password_confirmation" type="password"
               class="form-control form-control-sm" required>
      </div>

      <button type="submit" class="btn-mimi nut-vang w-100">Đổi mật khẩu</button>
    </form>
  </div>
</div>
