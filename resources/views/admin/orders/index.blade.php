@extends('layouts.admin')

@section('content')
<style>
  /* mọi th nền #4ab3af, viền trắng 2px, chữ trắng, căn giữa */
  .table.table-bordered.align-middle th {
    background-color: #4ab3af !important;
    border: 2px solid #fff      !important;
    color: #fff                 !important;
    text-align: center;
    vertical-align: middle;
  }
  /* mọi td căn giữa */
  .table.table-bordered.align-middle td {
    text-align: center;
    vertical-align: middle;
  }
  /* với những td có colspan thì canh trái, bỏ padding */
  .table.table-bordered.align-middle td[colspan] {
    text-align: left;
    padding-left: 0 !important;
  }
  /* Base style cho toàn bộ pagination */
.pagination .page-item .page-link {
  background-color: #4ab3af;
  color: white;
  border: none;              /* nếu không muốn viền */
  margin: 0 2px;             /* khoảng cách giữa các nút */
}

/* Hover state */
.pagination .page-item:not(.active) .page-link:hover {
  background-color: #3a958f; /* đậm hơn 1 chút khi hover */
  color: white;
  text-decoration: none;
}

/* Style cho nút đang active */
.pagination .page-item.active .page-link {
  background-color: white;
  color: #4ab3af;
  font-weight: bold;
  cursor: default; 
  border:1px solid #4ab3af;        
}

/* Disabled state (Prev/Next khi hết) */
.pagination .page-item.disabled .page-link {
  opacity: 0.5;
  pointer-events: none;
}
  .status-cancelled {
    background-color: #f8d7da !important;  /* đỏ nhạt */
    color: #721c24     !important;          /* đỏ đậm chữ */
  }
  /* Ghi chú admin nổi bật */
.table tbody tr:nth-child(odd) td[colspan] {
  background: #f9f9f9;
}
.customer-note { background:#e9f7ef; }
.admin-note    { background:#fce8e6; }
.conversation-messages {
  max-height: 200px;
  overflow-y: auto;
}

</style>

<div class="container py-4">
  <h2 class="mb-4" style="color: #b83232; font-size: 3rem;">Danh sách Đơn hàng</h2>

  {{-- filter/search --}}
  <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3 mb-4 align-items-end justify-content-end">
    <div class="col-auto">
      <input type="text" id="q" name="q" value="{{ $search ?? '' }}" class="form-control" placeholder="Tên, email, SĐT, mã đơn...">
    </div>
    <div class="col-auto">
      <button type="submit" class="btn-mimi nut-vang">Go!</button>
    </div>
  </form>

  <table class="table table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th>Mã đơn hàng</th>
        <th>Tài khoản</th>
        <th>Khách</th>
        <th>Điện thoại</th>
        <th>Tổng</th>
        <th>Thanh toán</th>
        <th>Mã vận đơn</th>
        <th>Trạng thái</th>
        <th>Ngày</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orders as $o)
        {{-- 1) Row chính --}}
        <tr>
          <td>{{ $o->order_code }}</td>
          <td>{{ $o->user->email ?? '— Khách vãng lai' }}</td>
          <td>
            {{ $o->fullname }}<br>
            <small>{{ $o->address }}</small>
          </td>
          <td>{{ $o->phone }}</td>
          <td>{{ number_format($o->total,0,',','.') }}₫</td>
          <td>
            @if($o->payment_method=='cod') COD
            @else CK<br><small>{{ $o->bank_ref }}</small>
            @endif
          </td>
          <td class="{{ $o->status === 'cancelled' ? 'bg-light' : '' }}">
            @if($o->status !== 'cancelled')
              <form action="{{ route('admin.orders.update', $o) }}" method="POST" class="d-flex">
                @csrf @method('PUT')
                <input type="text" name="tracking_number" value="{{ $o->tracking_number }}" class="form-control form-control-sm me-1" placeholder="Mã vận đơn">
                <button class="btn-mimi nut-xanh-la">Lưu</button>
              </form>
            @else
              <input type="text" class="form-control form-control-sm text-muted" disabled value="{{ $o->tracking_number ?? '—' }}">
            @endif
          </td>
          <td>
            <form action="{{ route('admin.orders.update', $o) }}" method="POST">
              @csrf @method('PUT')
              <select name="status" class="form-select form-select-sm {{ $o->status==='cancelled'?'status-cancelled':'' }}" onchange="this.form.submit()">
                <option value="pending"   {{ $o->status=='pending'   ? 'selected':'' }}>Đã tiếp nhận</option>
                <option value="shipping"  {{ $o->status=='shipping'  ? 'selected':'' }}>Đang giao hàng</option>
                <option value="done"      {{ $o->status=='done'      ? 'selected':'' }}>Đã giao hàng</option>
                <option value="cancelled" {{ $o->status=='cancelled' ? 'selected':'' }}>Đã hủy</option>
              </select>
            </form>
          </td>
          <td>{{ $o->created_at->format('d/m H:i') }}</td>
        </tr>

        {{-- 2) Row danh sách sản phẩm + options --}}
        <tr>
          <td colspan="5">
            <ul class="mb-0">
              @foreach($o->items as $it)
                <li>
                  {{ $it->product->name }} &times; {{ $it->quantity }}
                  @if($it->options)
                    <ul class="mb-1 ps-3">
                      @foreach(\App\Models\OptionValue::whereIn('id', $it->options)->with('type')->get() as $v)
                        <li>{{ $v->type->name }}: {{ $v->value }}</li>
                      @endforeach
                    </ul>
                  @endif
                </li>
              @endforeach
            </ul>
          </td>
          {{-- 3) Row conversation (ghi chú khách – phản hồi admin) --}}

          <td colspan="4">
            <div class="conversation-container">
              <!-- vùng messages scroll -->
              <div class="conversation-messages">
                @foreach($o->notes as $n)
                  <div class="msg {{ $n->is_admin ? 'admin-note' : 'customer-note' }} mb-2 p-2 rounded">
                    <small>
                      {{ $n->created_at->format('H:i d/m') }} —
                      {{ $n->is_admin ? 'Admin' : 'Khách' }}
                    </small>
                    <p class="mb-0">{{ $n->message }}</p>
                  </div>
                @endforeach
              </div>
              <!-- form luôn ở đáy -->
              <form action="{{ route('admin.orders.reply', $o) }}"
                    method="POST"
                    class="conversation-form mt-2 d-flex gap-3">
                @csrf
                <textarea name="admin_note"
                          class="form-control form-control-sm"
                          rows="2"
                          placeholder="Nhập phản hồi…"></textarea>
                <button class="btn-mimi nut-xanh">Gửi</button>
              </form>
            </div>
          </td>
        </tr>


      @endforeach
    </tbody>
  </table>
  @php
    $current = $orders->currentPage();
    $last    = $orders->lastPage();
    $pages   = [];

    if ($last <= 5) {
        // nếu tổng trang ≤5 thì show hết
        for ($i = 1; $i <= $last; $i++) {
            $pages[] = $i;
        }
    } elseif ($current <= 2) {
        // đang ở trang 1 hoặc 2
        $pages = [1, 2, 3, $last - 1, $last];
    } elseif ($current >= $last - 1) {
        // đang ở trang cuối hoặc trước cuối
        $pages = [1, 2, $last - 2, $last - 1, $last];
    } else {
        // ở giữa
        $pages = [1, $current - 1, $current, $current + 1, $last];
    }
@endphp

<nav>
  <ul class="pagination justify-content-center">
    {{-- Prev --}}
    <li class="page-item {{ $current == 1 ? 'disabled' : '' }}">
      <a class="page-link" href="{{ $orders->url($current - 1) }}">«</a>
    </li>

    {{-- Pages --}}
    @foreach($pages as $p)
      <li class="page-item {{ $p == $current ? 'active' : '' }}">
        @if($p == $current)
          <span class="page-link">{{ $p }}</span>
        @else
          <a class="page-link" href="{{ $orders->url($p) }}">{{ $p }}</a>
        @endif
      </li>
    @endforeach

    {{-- Next --}}
    <li class="page-item {{ $current == $last ? 'disabled' : '' }}">
      <a class="page-link" href="{{ $orders->url($current + 1) }}">»</a>
    </li>
  </ul>
</nav>
</div>
@endsection
