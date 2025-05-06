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

</style>

<div class="container py-4">
  <h2 class="mb-4" style="color: #b83232; font-size: 3rem;">Danh sách Đơn hàng</h2>

  <form method="GET"
      action="{{ route('admin.orders.index') }}"
      class="row g-3 mb-4 align-items-end justify-content-end">
  <div class="col-auto">
    <!-- <label for="q" class="form-label">Tìm kiếm</label> -->
    <input type="text"
           id="q"
           name="q"
           value="{{ $search ?? '' }}"
           class="form-control"
           placeholder="Tên, email, SĐT, mã đơn...">
  </div>
  <div class="col-auto">
    <button style="padding: 0.4rem 1rem; font-size: 1rem;" type="submit" class="btn-mimi nut-vang">Go!</button>
  </div>
</form>

  <table class="table table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th>Mã đơn hàng</th><th>Tài khoản</th><th>Khách</th><th>Điện thoại</th><th>Tổng</th>
        <th>Thanh toán</th><th>Mã vận đơn</th><th>Trạng thái</th><th>Ngày</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orders as $o)
        <tr>
          {{-- 1. Mã đơn --}}
          <td>{{ $o->order_code }}</td>
          {{-- 1b. Tài khoản --}}
          <td>
            @if($o->user)
              {{ $o->user->email }}
            @else
              <span class="text-muted">Khách không có tài khoản</span>
            @endif
          </td>
          {{-- 2. Khách & địa chỉ --}}
          <td>
            {{ $o->fullname }}<br>
            <small>{{ $o->address }}</small>
          </td>

          {{-- 3. Điện thoại --}}
          <td>{{ $o->phone }}</td>

          {{-- 4. Tổng tiền --}}
          <td>{{ number_format($o->total,0,',','.') }}₫</td>

          {{-- 5. Thanh toán --}}
          <td>
            @if($o->payment_method=='cod')
              COD
            @else
              CK<br>
              <small>{{ $o->bank_ref }}</small>
            @endif
          </td>

          {{-- 6. Tracking number (inline edit) --}}
          <td>
            <form action="{{ route('admin.orders.update', $o) }}" method="POST" class="d-flex">
              @csrf
              @method('PUT')
              <input
                type="text"
                name="tracking_number"
                value="{{ $o->tracking_number }}"
                class="form-control form-control-sm me-1"
                placeholder="Mã vận đơn">
              <button style="padding: 0.4rem 1rem; font-size: 1rem;" class="btn-mimi nut-xanh-la">Lưu</button>
            </form>
          </td>

          {{-- 7. Status dropdown (auto-submit) --}}
          <td>
            <form action="{{ route('admin.orders.update', $o) }}" method="POST">
              @csrf
              @method('PUT')
              <select
                name="status"
                class="form-select form-select-sm"
                onchange="this.form.submit()">
                <option value="pending"  {{ $o->status=='pending'  ? 'selected' : '' }}>Đã tiếp nhận</option>
                <option value="shipping" {{ $o->status=='shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                <option value="done"     {{ $o->status=='done'     ? 'selected' : '' }}>Đã giao hàng</option>
              </select>
            </form>
          </td>

          {{-- 8. Ngày tạo --}}
          <td>{{ $o->created_at->format('d/m H:i') }}</td>
        </tr>

        {{-- Chi tiết các item trong đơn --}}
        <tr>
          <td colspan="8">
            <ul class="mb-0">
              @foreach($o->items as $it)
                <li>
                  {{ $it->product->name }} × {{ $it->quantity }}
                  @if($it->options)
                    <ul class="mb-0 ps-3">
                      @php
                        $vals = \App\Models\OptionValue::whereIn('id', $it->options)
                                                      ->with('type')
                                                      ->get();
                      @endphp
                      @foreach($vals as $v)
                        <li>{{ $v->type->name }}: {{ $v->value }}</li>
                      @endforeach
                    </ul>
                  @endif
                </li>
              @endforeach
            </ul>
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
