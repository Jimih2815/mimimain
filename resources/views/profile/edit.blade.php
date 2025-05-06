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
              role="tab">
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
    <div style="padding-top:2rem; background-color:#ffffff;" class="tab-pane fade"
        id="orders"
        role="tabpanel"
        aria-labelledby="orders-tab">
      @if($orders->isEmpty())
        <p>Chưa có đơn hàng nào.</p>
      @else
        <table class="table table-bordered text-center">
          <thead>
            <tr>
              <th>Mã đơn hàng</th>
              <th>Ảnh</th>
              <th>Sản phẩm</th>
              <th>Thanh toán</th>
              <th>Hình thức</th>
              <th>Mã vận đơn</th>
              <th>Trạng thái</th>
              <th>Ngày đặt</th>
            </tr>
          </thead>
          <tbody>
            @foreach($orders as $o)
              <tr>
                <td>{{ $o->order_code }}</td>
                <!-- Ảnh sản phẩm đầu tiên -->
                <td>
                  @php $it = $o->items->first(); @endphp
                  <img src="{{ asset('storage/'.$it->product->img) }}"
                      style="width:50px;height:50px;object-fit:cover;">
                </td>
                <!-- Tên + thuộc tính -->
                <td>
                  @foreach($o->items as $it)
                    {{ $it->product->name }} × {{ $it->quantity }}
                    @if($it->options)
                      <ul class="mb-1">
                        @foreach(\App\Models\OptionValue::whereIn('id', $it->options)->with('type')->get() as $v)
                          <li>{{ $v->type->name }}: {{ $v->value }}</li>
                        @endforeach
                      </ul>
                    @endif
                    <hr class="my-1">
                  @endforeach
                </td>
                <td>{{ number_format($o->total,0,',','.') }}₫</td>
                <td>{{ $o->payment_method=='cod'?'COD':'Chuyển khoản' }}</td>
                <td><div class="d-flex  flex-column">{{ $o->tracking_number ?? '—' }} <a style="color:#d1a029;" href="https://spx.vn/track" target="_blank">Tra cứu đơn hàng</a></div></td>
                @php
                  $statusLabels = [
                    'pending'  => 'Đã tiếp nhận',
                    'shipping' => 'Đang giao hàng',
                    'done'     => 'Đã nhận hàng',
                  ];
                  $statusClass = 'status-' . $o->status;
                @endphp

                <td class="{{ $statusClass }}">
                  {{ $statusLabels[$o->status] ?? ucfirst($o->status) }}
                </td>
                <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
        {{ $orders->links() }}
      @endif
    </div>


    
  {{-- Trợ giúp --}}
  <div class="tab-pane fade" id="help" role="tabpanel" aria-labelledby="help-tab">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title mb-4 mt-4">Yêu cầu trợ giúp của bạn</h4>

        @php
          use Illuminate\Support\Str;

          $orderedRequests = $user->helpRequests
            ->sort(function($a, $b) {
              $aDone = $a->status === 'Hoàn thành';
              $bDone = $b->status === 'Hoàn thành';

              // 1) Đẩy nhóm 'Hoàn thành' xuống cuối
              if ($aDone !== $bDone) {
                return $aDone <=> $bDone;
              }
              // 2) Trong nhóm 'Hoàn thành', sắp mới → cũ
              if ($aDone) {
                return $b->created_at->timestamp <=> $a->created_at->timestamp;
              }
              // 3) Nhóm chưa hoàn thành, sắp cũ → mới
              return $a->created_at->timestamp <=> $b->created_at->timestamp;
            })
            ->values();
        @endphp

        {{-- Nếu không có yêu cầu nào --}}
        @if($orderedRequests->isEmpty())
          <p>Bạn chưa gửi yêu cầu trợ giúp nào.</p>

        {{-- Ngược lại, hiển thị bảng --}}
        @else
          <table class="table table-bordered text-center">
            <thead class="table-light">
              <tr>
                <th style="width:5%;">STT</th>
                <th style="width:40%;">Nội dung</th>
                <th style="width:40%;">Phản hồi</th>
                <th style="width:15%;">Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              @foreach($orderedRequests as $req)
                @php
                  $statusClass = 'status-' . Str::slug($req->status, '-');
                @endphp
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $req->message }}</td>
                  <td>{{ $req->response ?? '—' }}</td>
                  <td class="text-center {{ $statusClass }}">
                    {{ $req->status }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash;        // ví dụ "#orders"
    if (!hash) return;

    // Chọn button có data-bs-target trùng hash
    const btn = document.querySelector(
      `#profileTabs button[data-bs-target="${hash}"]`
    );
    if (btn) {
      // active tab header
      document.querySelectorAll('#profileTabs .nav-link').forEach(el => {
        el.classList.remove('active');
      });
      btn.classList.add('active');

      // active tab content
      document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('show', 'active');
      });
      const panel = document.querySelector(hash);
      if (panel) {
        panel.classList.add('show', 'active');
      }
    }
  });
</script>
@endpush

