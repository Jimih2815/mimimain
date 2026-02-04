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

.order-option-line {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin-bottom: 0.2rem;
}

.order-option-thumb-btn {
  border: 0;
  background: transparent;
  padding: 0;
  line-height: 0;
  border-radius: 6px;
  cursor: zoom-in;
}

.order-option-thumb {
  width: 30px;
  height: 30px;
  object-fit: cover;
  border: 1px solid #ced4da;
  border-radius: 6px;
  display: block;
}

#orderOptionPreviewModal .modal-dialog {
  max-width: min(92vw, 720px);
}

.order-option-preview-img {
  width: 100%;
  max-height: 78vh;
  object-fit: contain;
  background: #f8f9fa;
  border-radius: 10px;
}

</style>

<div class="container py-4 trang-admin-orders">
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

  <div class="admin-orders-table-scroll">
  <table class="table table-bordered align-middle admin-orders-table">
    <thead class="table-light">
      <tr>
        <th>Mã đơn hàng</th><th>Khách</th><th>Địa chỉ</th><th>Điện thoại</th><th>Tổng</th>
        <th>Thanh toán</th><th>Mã vận đơn</th><th>Trạng thái</th><th>Ngày</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orders as $o)
        <tr>
          {{-- 1. Mã đơn --}}
          <td>{{ $o->order_code }}</td>
          {{-- 2. Khách --}}
          <td>{{ $o->fullname }}</td>

          {{-- 3. Địa chỉ --}}
          <td>{{ $o->address }}</td>

          {{-- 4. Điện thoại --}}
          <td>{{ $o->phone }}</td>

          {{-- 5. Tổng tiền --}}
          <td>{{ number_format($o->total,0,',','.') }}₫</td>

          {{-- 6. Thanh toán --}}
          <td>
            @if($o->payment_method=='cod')
              COD
            @else
              CK<br>
              <small>{{ $o->bank_ref }}</small>
            @endif
          </td>

          {{-- 7. Tracking number (inline edit) --}}
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

          {{-- 8. Status dropdown (auto-submit) --}}
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

          {{-- 9. Ngày tạo --}}
          <td>{{ $o->created_at->format('d/m H:i') }}</td>
        </tr>

        {{-- Chi tiết các item trong đơn --}}
        <tr>
          <td colspan="9">
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
                        @php
                          $optionImgUrl = $v->option_img ? asset('storage/'.$v->option_img) : null;
                        @endphp
                        <li class="order-option-line">
                          @if($optionImgUrl)
                            <button type="button"
                                    class="order-option-thumb-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#orderOptionPreviewModal"
                                    data-option-img="{{ $optionImgUrl }}"
                                    data-option-label="{{ $v->type->name }}: {{ $v->value }}"
                                    aria-label="Xem ảnh option {{ $v->value }}">
                              <img src="{{ $optionImgUrl }}"
                                   alt="{{ $v->value }}"
                                   class="order-option-thumb">
                            </button>
                          @endif
                          <span>{{ $v->type->name }}: {{ $v->value }}</span>
                        </li>
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
  </div>

  <div class="modal fade" id="orderOptionPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header py-2">
          <h5 class="modal-title fs-6" id="orderOptionPreviewLabel">Ảnh option</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img src="" alt="Ảnh option" class="order-option-preview-img" id="orderOptionPreviewImg">
        </div>
      </div>
    </div>
  </div>

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

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const previewModalEl = document.getElementById('orderOptionPreviewModal');
    if (!previewModalEl) return;

    previewModalEl.addEventListener('show.bs.modal', function (event) {
      const trigger = event.relatedTarget;
      if (!trigger) return;

      const imgEl = previewModalEl.querySelector('#orderOptionPreviewImg');
      const titleEl = previewModalEl.querySelector('#orderOptionPreviewLabel');
      const imgSrc = trigger.getAttribute('data-option-img') || '';
      const label = trigger.getAttribute('data-option-label') || 'Ảnh option';

      imgEl.src = imgSrc;
      imgEl.alt = label;
      titleEl.textContent = label;
    });

    previewModalEl.addEventListener('hidden.bs.modal', function () {
      const imgEl = previewModalEl.querySelector('#orderOptionPreviewImg');
      if (imgEl) imgEl.src = '';
    });
  });
</script>
@endpush

@endsection
