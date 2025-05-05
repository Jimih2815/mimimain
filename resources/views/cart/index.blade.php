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
            <a href="{{ route('products.show', $item['slug']) }}">
              <img src="{{ asset('storage/'.$item['image']) }}"
                  class="img-fluid rounded anh-san-pham"
                  alt="{{ $item['name'] }}">
            </a>

              <div class="d-flex tang-giam-va-tim">
                <div class="tang-giam-cont">
                  <form action="{{ route('cart.update', $key) }}"
                        method="POST"
                        class="d-inline-block xoa-border">
                    @csrf
                    <button
                      type="button"
                      class="btn btn-outline-secondary btn-sm xoa-border qty-btn minus"
                      data-key="{{ $key }}"
                      data-action="dec"
                    >
                      @if($item['quantity'] > 1)
                        &minus;
                      @else
                        <i class="fa fa-trash"></i>
                      @endif
                    </button>

                    <span
                      class="item-qty btn btn-outline-secondary btn-sm mx-1 disabled xoa-border"
                    >
                      {{ $item['quantity'] }}
                    </span>

                    <button
                      type="button"
                      class="btn btn-outline-secondary btn-sm xoa-border qty-btn plus"
                      data-key="{{ $key }}"
                      data-action="inc"
                    >
                      +
                    </button>

                  </form>
                </div>

                @php
                  // Kiểm tra xem đã favorite chưa: auth → DB, guest → session
                  $isFav = auth()->check()
                    ? auth()->user()->favorites->contains($item['product_id'])
                    : in_array($item['product_id'], session('favorites', []));
                @endphp

                <button
                  type="button"
                  class="btn-favorite trai-tim"
                  data-id="{{ $item['product_id'] }}"
                >
                  <i class="{{ $isFav ? 'fas text-danger' : 'far text-muted' }} fa-heart"></i>
                </button>

              </div>            
            </div>

            {{-- Thông tin sản phẩm --}}
            <div class="ms-4 flex-grow-1">
              <div class="d-flex justify-content-between">
              <h5 class="mb-1">
              <a href="{{ route('products.show', $item['slug']) }}"
                class="text-decoration-none ten-san-pham-an-duoc">
                {{ $item['name'] }}
              </a>
              </h5>
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

            {{-- Tin FreeShip (ẩn mặc định) --}}
            <p id="freeship-msg"
              class="text-free-ship mb-2">
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
  // === 1) Checkout summary logic ===
  const form        = document.getElementById('checkout-form');
  const btn         = document.getElementById('checkout-button');
  const warnEl      = document.getElementById('checkout-warning');
  const boxes       = document.querySelectorAll('input.rowCheck');
  const subtotalEl  = document.getElementById('summary-subtotal');
  const shipEl      = document.getElementById('summary-shipping');
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
    shipEl.textContent     = (shipping === 0 && subtotal > 0)
                             ? 'Free'
                             : shipping.toLocaleString('vi-VN') + '₫';
    grandEl.textContent    = grand.toLocaleString('vi-VN') + '₫';
  }

  form.addEventListener('submit', e => {
    recalcSummary();
    if (!Array.from(boxes).some(cb => cb.checked)) {
      e.preventDefault();
      warnEl.style.display = 'block';
      btn.style.border     = '1px solid #3a9b98';
    }
  });

  boxes.forEach(cb => 
    cb.addEventListener('change', () => {
      recalcSummary();
      warnEl.style.display = 'none';
      btn.style.border     = '';
    })
  );

  // === 2) AJAX update quantity & remove ===
  const token = document.querySelector('meta[name="csrf-token"]').content;
  const updateUrl = "{{ url('/cart/update') }}"; 

  document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      const rowKey = btn.dataset.key;
      const action = btn.dataset.action;

      try {
        const res = await fetch(`${updateUrl}/${rowKey}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN':     token,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type':     'application/json',
            'Accept':           'application/json',
          },
          body: JSON.stringify({ action })
        });

        if (!res.ok) throw new Error('Network error');
        const data = await res.json();
        if (data.success) {
          const row   = btn.closest('.d-flex.align-items-start');
          const qtyEl = row.querySelector('.item-qty');
          qtyEl.textContent = data.quantity;

          // Cập nhật dataset.qty để summary đúng
          const cb = row.querySelector('input.rowCheck');
          if (cb) cb.dataset.qty = data.quantity;

          // **MỚI**: Cập nhật icon cho nút "minus" / "trash"
          const decBtn = row.querySelector('.qty-btn.minus');
          if (data.quantity > 1) {
            decBtn.innerHTML = '&minus;';                     // hiển dấu trừ
          } else {
            decBtn.innerHTML = '<i class="fa fa-trash"></i>'; // hiển icon thùng rác
          }

          // Nếu quantity = 0 thì remove mục
          if (data.quantity === 0) {
            row.remove();
          }

          // Cập nhật lại summary
          recalcSummary();
        }
      } catch (err) {
        console.error('Lỗi cập nhật giỏ hàng:', err);
      }
    });
  });



  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  // === Xử lý nút favorite trên trang Cart ===
  document.querySelectorAll('.btn-favorite').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      fetch(`/favorites/toggle/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
        },
      })
      .then(res => res.json())
      .then(json => {
        const icon = btn.querySelector('i.fa-heart');
        if (json.added) {
          icon.classList.replace('far', 'fas');
        } else {
          icon.classList.replace('fas', 'far');
        }
      })
      .catch(console.error);
    });
  });
});
</script>
@endpush





