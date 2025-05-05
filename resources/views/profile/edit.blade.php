{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title','Hồ sơ của tôi')

@section('content')
<div class="w-70 mx-auto mt-5 mb-5 trang-thong-tin">

  {{-- Nav tabs --}}
  <ul class="nav nav-tabs" id="profileTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active"
              id="info-tab"
              data-bs-toggle="tab"
              data-bs-target="#info"
              type="button"
              role="tab"
              aria-controls="info"
              aria-selected="true">
        Thông tin
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link"
              id="orders-tab"
              data-bs-toggle="tab"
              data-bs-target="#orders"
              type="button"
              role="tab"
              aria-controls="orders"
              aria-selected="false">
        Theo dõi đơn hàng
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link"
              id="help-tab"
              data-bs-toggle="tab"
              data-bs-target="#help"
              type="button"
              role="tab"
              aria-controls="help"
              aria-selected="false">
        Trợ giúp
      </button>
    </li>
  </ul>

  <div class="tab-content" id="profileTabsContent">
    {{-- Thông tin --}}
    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title mb-4 mt-4">Thông tin tài khoản &amp; Đổi mật khẩu</h4>

          @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
          @endif
          @if(session('password_status'))
            <div class="alert alert-success">{{ session('password_status') }}</div>
          @endif

          {{-- Chỉnh sửa hồ sơ --}}
          <form method="POST" action="{{ route('profile.update') }}" class="mb-4">
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

            <button type="submit" class="btn-mimi nut-xanh w-50 d-flex justify-content-center align-items-center mx-auto">
              Cập nhật thông tin
            </button>
          </form>

          <hr>

          {{-- Đổi mật khẩu --}}
          <form class="mt-5" method="POST" action="{{ route('profile.password.update') }}">

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

            <button type="submit" class="btn-mimi nut-vang w-50 d-flex justify-content-center align-items-center mx-auto">
              Đổi mật khẩu
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- Theo dõi đơn hàng --}}
    <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
      <div class="card">
        <div class="card-body">
          <p>(Chức năng đang phát triển…)</p>
        </div>
      </div>
    </div>

    {{-- Trợ giúp --}}
    <div class="tab-pane fade" id="help" role="tabpanel" aria-labelledby="help-tab">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title mb-4 mt-4">Yêu cầu trợ giúp của bạn</h4>

          @if($user->helpRequests->isEmpty())
            <p>Bạn chưa gửi yêu cầu trợ giúp nào.</p>
          @else
            <table class="table table-bordered  text-center">
              <thead class="table-light">
                <tr>
                  <th style="width:5%;">STT</th>
                  <th style="width:40%;">Nội dung</th>
                  <th style="width:40%;">Phản hồi</th>
                  <th style="width:15%;">Trạng thái</th>
                </tr>
              </thead>
              <tbody>
                @foreach($user->helpRequests as $i => $req)
                  <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $req->message }}</td>
                    <td>{{ $req->response ?? '—' }}</td>
                    <td>{{ $req->status }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
