{{-- resources/views/cart/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container trang-gio-hang">
  @if (count($cart))
    <div class="row">
      {{-- ===== Cột trái: Bag ===== --}}
      <div class="col-md-8">
        <h3 class="mb-4">Sản Phẩm</h3>

        @foreach($cart as $key => $item)
          <div class="d-flex align-items-start mb-4">
            {{-- Checkbox chọn sản phẩm --}}
            <div class="me-3">
              <input 
                type="checkbox"
                name="selected_ids[]"
                value="{{ $key }}"
                form="checkout-form"
                class="rowCheck"
                data-price="{{ $item['price'] }}"
                data-qty="{{ $item['quantity'] }}">
            </div>

            {{-- Ảnh + nút tăng/giảm --}}
            <div class="text-center">
              <img src="{{ asset('storage/'.$item['image']) }}"
                   class="img-fluid rounded anh-san-pham"
                   alt="{{ $item['name'] }}">

              <div class="d-flex tang-giam-va-tim">
                <div class="tang-giam-cont">
                  <form action="{{ route('cart.update', $key) }}"
                        method="POST"
                        class="d-inline-block xoa-border">
                    @csrf
                    <button type="submit"
                            name="action" value="dec"
                            class="btn btn-outline-secondary btn-sm xoa-border">
                      @if($item['quantity'] > 1)
                        &minus;
                      @else
                        <i class="fa fa-trash xoa-border"></i>
                      @endif
                    </button>
                  </form>

                  <span class="btn btn-outline-secondary btn-sm mx-1 disabled xoa-border">
                    {{ $item['quantity'] }}
                  </span>

                  <form action="{{ route('cart.update', $key) }}"
                        method="POST"
                        class="d-inline-block xoa-border">
                    @csrf
                    <button type="submit"
                            name="action" value="inc"
                            class="btn btn-outline-secondary btn-sm xoa-border">
                      +
                    </button>
                  </form>
                </div>

                <button type="button" class="trai-tim">
                  <i class="fa fa-heart"></i>
                </button>
              </div>            
            </div>

            {{-- Thông tin sản phẩm --}}
            <div class="ms-4 flex-grow-1">
              <div class="d-flex justify-content-between">
                <h5 class="mb-1">{{ $item['name'] }}</h5>
                <span class="fw-bold">
                  {{ number_format($item['price'],0,',','.') }}₫
                </span>
              </div>

              @php
                $vals = \App\Models\OptionValue::whereIn('id', $item['options'] ?? [])
                          ->with('type')->get();
              @endphp
              @foreach($vals as $val)
                <div class="text-muted">
                  <strong>{{ $val->type->name }}:</strong>
                  {{ $val->value }}
                  @if($val->extra_price)
                    (+{{ number_format($val->extra_price,0,',','.') }}₫)
                  @endif
                </div>
              @endforeach
            </div>         
          </div>
          <hr>
        @endforeach
      </div>

      {{-- ===== Cột phải: Summary + form thanh toán ===== --}}
      <div class="col-md-4 order-summary">
        <h3 class="mb-4">Thanh Toán</h3>
        <form id="checkout-form"
              action="{{ route('checkout.show') }}"
              method="GET">
          @csrf

          <div class="card">
            <div class="card-body">
              {{-- Thành Tiền --}}
              <p class="d-flex justify-content-between">
                <span>Thành Tiền</span>
                <span id="summary-subtotal">0₫</span>
              </p>

              {{-- Phí Ship --}}
              <p class="d-flex justify-content-between">
                  <span>Phí Ship</span>
                  <span id="summary-shipping">0₫</span>
                </p>
                {{-- Luôn hiển thị tin khuyến mãi --}}
                <p class="text-free-ship mb-2">
                  (FreeShip với đơn hàng &gt; 199.000₫)
              </p>

              <hr>

              {{-- Tổng Cộng --}}
              <p class="d-flex justify-content-between fw-bold">
                <span>Tổng Cộng</span>
                <span id="summary-grandtotal">0₫</span>
              </p>

              {{-- Hộp cảnh báo ẩn --}}
              <p id="checkout-warning"
                 style="display:none; color:#b83232; margin-bottom:8px;">
                Bạn vui lòng chọn sản phẩm trước khi thanh toán nha
              </p>

              {{-- Nút Thanh toán --}}
              <button type="submit"
                      id="checkout-button"
                      class="btn w-100 nut-thanh-toan">
                Thanh toán
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  @else
    <p class="text-center">Giỏ hàng trống!</p>
  @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form        = document.getElementById('checkout-form');
  const btn         = document.getElementById('checkout-button');
  const warnEl      = document.getElementById('checkout-warning');
  const boxes       = document.querySelectorAll('input.rowCheck');
  const subtotalEl  = document.getElementById('summary-subtotal');
  const shipEl      = document.getElementById('summary-shipping');
  const freeEl      = document.getElementById('freeship-msg');
  const grandEl     = document.getElementById('summary-grandtotal');

  function recalcSummary() {
    let subtotal = 0;
    boxes.forEach(cb => {
      if (cb.checked) {
        const price = parseInt(cb.dataset.price) || 0;
        const qty   = parseInt(cb.dataset.qty)   || 1;
        subtotal += price * qty;
      }
    });

    const shipping = subtotal > 199000 
      ? 0 
      : (subtotal === 0 ? 0 : 20000);
    const grand = subtotal + shipping;

    subtotalEl.textContent = subtotal.toLocaleString('vi-VN') + '₫';

    if (shipping === 0 && subtotal > 199000) {
      shipEl.textContent = 'Free';
      freeEl.style.display = 'block';
    } else {
      shipEl.textContent = shipping.toLocaleString('vi-VN') + '₫';
      freeEl.style.display = 'none';
    }

    grandEl.textContent = grand.toLocaleString('vi-VN') + '₫';
  }

  // Kiểm tra khi submit
  form.addEventListener('submit', e => {
    recalcSummary();
    const anyChecked = Array.from(boxes).some(cb => cb.checked);
    if (!anyChecked) {
      e.preventDefault();
      warnEl.style.display = 'block';
      btn.style.border = '1px solid #3a9b98';
    }
  });

  // Cập nhật ngay khi check/uncheck
  boxes.forEach(cb => 
    cb.addEventListener('change', () => {
      recalcSummary();
      warnEl.style.display = 'none';
      btn.style.border = '';
    })
  );

  // Khởi tạo summary ban đầu
  recalcSummary();
});
</script>
@endpush
