{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title','Hồ sơ của tôi')

@section('content')
<style>
  .nut-huy {

    font-size: 1rem !important;
    font-weight: 600;
    width: 5rem;
    padding: 0.2rem 0.5rem !important;
    white-space: nowrap;
    border-radius: 50px !important;
    color: white;
    background-color: #b83232;

  }
  .btn-note {
        font-size: 1rem !important;
    font-weight: 600;
 width: 5rem;
    padding: 0.2rem 0.5rem !important;
    white-space: nowrap;
    border-radius: 50px !important;
    color: white;
    background-color: #4ab3af;
    margin-bottom: 1rem;
  }
  .btn-note:hover {
        background-color:rgb(158, 228, 225);

  }
  .nut-huy:hover {
        background-color:rgb(231, 135, 135);

  }
  .da-huy {
        font-size: 1rem;
    font-weight: 600;
    width: 5rem;
    padding: 0.2rem 0.5rem !important;
    white-space: nowrap;
    border-radius: 50px !important;
    color: white;
    background-color:rgb(167, 157, 157);
  }
  .trang-thong-tin form {
    padding: 0;
  }
  .table td, .table th {
  vertical-align: middle !important;
  text-align: center;
}
.note-modal {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  /* ban đầu dịch phải 100% (ẩn ngoài view) */
  transform: translateX(100%);
  transition: transform .3s ease;
  z-index: 2000;
}
.note-modal.open {
  transform: translateX(0);
}
.note-content {
  background: #fff;
  border: 1px solid #ccc;
  border-radius: .25rem;
  box-shadow: 0 2px 8px rgba(0,0,0,.2);
  width: 50%;           /* bạn có thể tăng/giảm */
  max-width: 90%;
  display: flex;
  flex-direction: column;
  padding: 1rem;
  position: relative;
}

/* nút đóng */
.note-content .close {
  position: absolute;
  top: .5rem; right: .5rem;
  font-size: 1.2rem;
  border: none;
  background: transparent;
  cursor: pointer;
}

/* vùng hội thoại scroll */
.note-body {
  flex: 1;                /* chiếm hết không gian còn lại */
  overflow-y: auto;
  max-height: 500px;
  margin-bottom: .75rem;
  padding-right: .5rem;   /* dư ra 1 chút để scrollbar */
}
.customer-note {
  align-self: flex-start;
  background: #78b865;       /* Vàng - dễ thương, thân thiện */
  color: #ffffff;             /* Nâu đậm để chữ dễ đọc trên nền vàng */
  max-width: 50%;
  width: fit-content;
  word-break: break-word;
  border-radius: 25px;
  padding: 8px 12px;
  margin-left: auto;
  font-size: 1.2rem; 
  font-weight: 600;
}

.admin-note {
  align-self: flex-end;
  background: #4ab3af;       /* Xanh ngọc - màu chủ đạo */
  color: #ffffff;            /* Trắng cho nổi bật */
  max-width: 50%;
  width: fit-content;
  word-break: break-word;
  border-radius: 25px;
  padding: 8px 12px;
    font-size: 1.2rem; 
  font-weight: 600;
}
#nm-form {
      height: 40px;

}
.nut-gui {
  background: #ffba26;
    font-size: 1.1rem;
    font-weight: 700;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
}
.nut-gui:hover {
    background:rgb(247, 216, 150);

}
.chu-do {
  color: #b83232;
    font-size: 1.5rem;
    font-weight: 700;
}
.note-body::-webkit-scrollbar {
  width: 8px;
}

.note-body::-webkit-scrollbar-track {
  background: #f0f0f0; /* Màu nền thanh trượt */
  border-radius: 4px;
}

.note-body::-webkit-scrollbar-thumb {
  background:rgb(122, 207, 204); /* Màu chính của trang */
  border-radius: 4px;
}

.note-body::-webkit-scrollbar-thumb:hover {
  background: #369a98; /* Hover: tối hơn 1 tí */
}

</style>
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
         @if(session('order_status'))
            <div class="alert alert-success">{{ session('order_status') }}</div>
          @endif
          @if(session('note_status'))
            <div class="alert alert-success">{{ session('note_status') }}</div>
          @endif

      @if($orders->isEmpty())
        <p>Chưa có đơn hàng nào.</p>
      @else
        <div id="orders-content">
          <table class="table table-bordered text-center">
            <thead>
              <tr>
                <th >Mã đơn hàng</th>
                <th>Ảnh</th>
                <th>Sản phẩm</th>
                <th>Thanh toán</th>
                <th>Hình thức</th>
                <th>Mã vận đơn</th>
                <th>Trạng thái</th>
                <th>Ngày đặt</th>
                <th style="width: 1rem;">Thao tác</th> 
              </tr>
            </thead>
            <tbody>
              @foreach($orders as $o)
                <tr>
                  <td>{{ $o->order_code }}</td>
                  <!-- Ảnh -->
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
                  <td>
                    <div class="d-flex flex-column">
                      {{ $o->tracking_number ?? '—' }}
                      <a style="color:#d1a029;" href="https://spx.vn/track" target="_blank">Tra cứu đơn hàng</a>
                    </div>
                  </td>
                  @php
                    $statusLabels = [
                      'pending'   => 'Đã tiếp nhận',
                      'shipping'  => 'Đang giao hàng',
                      'done'      => 'Đã nhận hàng',
                      'cancelled' => 'Đã hủy',       // Thêm nhãn mới
                    ];
                    $statusClass = 'status-' . $o->status;
                  @endphp
                  <td class="{{ $statusClass }}">
                    {{ $statusLabels[$o->status] ?? ucfirst($o->status) }}
                  </td>
                  <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
                  {{-- Thao tác --}}
                  <td>
                    {{-- nút Ghi chú --}}
                    <button type="button"
                            class="btn-mimi btn-note"
                            data-order-id="{{ $o->id }}"
                            data-order-code="{{ $o->order_code }}"
                            data-note="{{ $o->note }}">
                      Ghi chú
                    </button>
                    <div id="notes-data-{{ $o->id }}" style="display:none;">
                      @foreach($o->notes as $n)
                        <div class="{{ $n->is_admin? 'admin-note':'customer-note' }} mb-2 p-2 ">
                         
                            <small class="ps-2">{{ $n->created_at->format('H:i d/m') }} — {{ $n->is_admin? 'Admin':'Bạn' }}</small>
                            <p class="mb-0 ps-2">{{ $n->message }}</p>
                          
                        </div>
                      @endforeach
                    </div>

                    @if($o->status === 'pending')
                  {{-- Nút hủy bình thường --}}
                  <form method="POST"
                        action="{{ route('orders.cancel', $o->id) }}"
                        onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn #{{ $o->order_code }}?');">
                    @csrf
                    <button type="submit" class="btn-mimi nut-huy">
                      Hủy đơn
                    </button>
                  </form>

                @elseif($o->status === 'cancelled')
                  {{-- Đã hủy rồi: disable --}}
                  <button type="button"
                          class="btn-mimi da-huy"
                          disabled>
                    Đã hủy
                  </button>

                @else
                  {{-- Đang giao hoặc đã nhận: chỉ show alert --}}
                  <button type="button"
                          class="btn-mimi nut-huy"
                          onclick="alert('Đơn hàng đã được bàn giao cho Shipper - không thể hủy đơn');">
                    Hủy đơn
                  </button>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      
        @php
    $current = $orders->currentPage();
    $last    = $orders->lastPage();
    $pages = [];

    if ($last <= 5) {
        // Nếu tổng trang ≤5 thì show hết
        for ($i = 1; $i <= $last; $i++) {
            $pages[] = $i;
        }
    } else {
        if ($current <= 2) {
            // Trang 1 hoặc 2: 1,2,3, last-1, last
            $pages = [1,2,3, $last-1, $last];
        } elseif ($current >= $last - 1) {
            // Trang penultimate hoặc last: 1,2, last-2, last-1, last
            $pages = [1,2, $last-2, $last-1, $last];
        } else {
            // Ở giữa: 1, current-1, current, current+1, last
            $pages = [1, $current-1, $current, $current+1, $last];
        }
    }
      @endphp

      <nav class="nut-pagi">
        <ul style="gap:2px;" class="pagination justify-content-center ">
          {{-- Nút Prev --}}
          <!-- <li class="page-item {{ $current==1?'disabled':'' }}">
            <a class="page-link" href="{{ $orders->url($current-1) }}">«</a>
          </li> -->

          {{-- Các trang --}}
          @foreach($pages as $p)
            <li class="page-item {{ $p==$current?'active':'' }}">
              @if($p == $current)
                <span class="page-link">{{ $p }}</span>
              @else
                <a class="page-link" href="{{ $orders->url($p) }}">{{ $p }}</a>
              @endif
            </li>
          @endforeach

          {{-- Nút Next --}}
          <!-- <li class="page-item {{ $current==$last?'disabled':'' }}">
            <a class="page-link" href="{{ $orders->url($current+1) }}">»</a>
          </li> -->
        </ul>
      </nav>
    </div>
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


{{-- nội dung chat --}}
<div id="note-modal" class="note-modal">
  <div class="note-content">
    <button class="close">&times;</button>
    <h5 class="chu-do">Đơn #<span id="nm-code"></span></h5>
    <div id="nm-old" class="note-body"><!-- JS sẽ inject cuộc hội thoại vào đây --></div>
    <form id="nm-form" class="d-flex gap-2" method="POST" action="">
      @csrf
      <textarea name="note"
                id="nm-input"
                class="form-control"
                rows="3"
                placeholder="Nhập ghi chú…"></textarea>
      <button type="submit" class="btn-mimi nut-gui">Gửi</button>
    </form>
  </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  //
  // 1) Giữ tab khi reload URL có #orders
  //
  const hash = window.location.hash;
  if (hash) {
    const btn = document.querySelector(`#profileTabs button[data-bs-target="${hash}"]`);
    if (btn) {
      document.querySelectorAll('#profileTabs .nav-link').forEach(el => el.classList.remove('active'));
      btn.classList.add('active');
      document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('show','active'));
      const panel = document.querySelector(hash);
      if (panel) panel.classList.add('show','active');
    }
  }

  //
  // 2) AJAX pagination cho đơn hàng
  //
  const ordersContent = document.getElementById('orders-content');
  ordersContent.addEventListener('click', e => {
    const a = e.target.closest('a.page-link');
    if (!a) return;
    e.preventDefault();
    fetch(a.href, { headers:{ 'X-Requested-With':'XMLHttpRequest' } })
      .then(r => r.text())
      .then(html => {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const newContent = doc.querySelector('#orders-content');
        ordersContent.innerHTML = newContent.innerHTML;
      })
      .catch(console.error);
  });
  window.addEventListener('popstate', () => {
    fetch(location.href, { headers:{ 'X-Requested-With':'XMLHttpRequest' } })
      .then(r => r.text())
      .then(html => {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        ordersContent.innerHTML = doc.querySelector('#orders-content').innerHTML;
      });
  });

  //
  // 3) Modal “Ghi chú”
  //
  const modal   = document.getElementById('note-modal');
  const nmCode  = document.getElementById('nm-code');
  const nmOld   = document.getElementById('nm-old');
  const nmInput = document.getElementById('nm-input');
  const nmForm  = document.getElementById('nm-form');

  // Mở modal khi click nút Ghi chú
  document.querySelectorAll('.btn-note').forEach(btn => {
    btn.addEventListener('click', () => {
      const id      = btn.dataset.orderId;
      nmCode.textContent = btn.dataset.orderCode;
      const convoHtml = document.getElementById(`notes-data-${id}`).innerHTML;
      nmOld.innerHTML    = convoHtml || '<em>—</em>';
      nmInput.value      = '';
      nmForm.action      = `/profile/orders/${id}/note`;
      modal.classList.add('open');
    });
  });

  // Đóng modal khi click nút ×
  modal.querySelector('.close').addEventListener('click', () => {
    modal.classList.remove('open');
  });

  // Đóng modal khi click ra ngoài .note-content
  modal.addEventListener('click', e => {
    if (e.target === modal) {
      modal.classList.remove('open');
    }
  });
});
</script>
@endpush
