{{-- resources/views/cart/index.blade.php --}}
<link rel="stylesheet" href="{{ Vite::asset('resources/scss/cart.scss') }}">

@extends('layouts.app-mobile')

@section('title', 'Giỏ hàng')

@section('content')
<style>
  .trang-gio-hang {
    padding: 1rem;
}
.trang-gio-hang .anh-san-pham {
    width: 150px;
    aspect-ratio: 1 / 1;
    height: auto;
    object-fit: cover;
}
.trang-gio-hang .xoa-border {
    border: 0px solid black !important;
    margin-bottom: 0;
}
.item-qty-input {
  padding: 0;
  text-align: center;
}
    .order-summary {
      position: sticky;
      bottom: 0;
      z-index: 1;
      background: #fff;          
      padding-top: 0.5rem;       
      box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
    }
    .card-body {
    flex: 1 1 auto;
    color: var(--bs-card-color);
    padding: 0.5rem;
}
hr {
  margin: 0.2rem 0 ;
}
</style>
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
                <div class="tang-giam-cont ">
                  <form action="{{ route('cart.update', $key) }}"
                        method="POST"
                        class="d-flex xoa-border">
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

                    <input
                      type="number"
                      name="quantity"
                      value="{{ $item['quantity'] }}"
                      min="1"
                      class="item-qty-input btn btn-outline-secondary btn-sm mx-1 xoa-border text-center"
                      style="width: 100%;"
                      onchange="this.form.submit()"
                      onkeydown="if(event.key === 'Enter'){ event.preventDefault(); this.form.submit(); }"
                    />


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
              <div class="d-flex flex-column justify-content-between">
              <h5 class="mb-1">
              <a href="{{ route('products.show', $item['slug']) }}"
                class="text-decoration-none ten-san-pham-an-duoc w-100">
                {{ $item['name'] }}
              </a>
              </h5>
                <span class="fw-bold mt-2">
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
        <h3 class="mb-1">Thanh Toán</h3>
        <form id="checkout-form"
              action="{{ route('checkout.show') }}"
              method="GET">
          @csrf

          <div class="card">
            <div class="card-body">
            {{-- Thành Tiền --}}
            <p class="d-flex justify-content-between mb-1">
              <span>Thành Tiền</span>
              <span id="summary-subtotal">0₫</span>
            </p>

            {{-- Phí Ship --}}
            <p class="d-flex justify-content-between mb-1">
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
            <p class="d-flex justify-content-between mb-1 fw-bold">
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
  const token     = document.querySelector('meta[name="csrf-token"]').content;
  const updateUrl = "{{ url('/cart/update') }}";

  // chung function gọi AJAX
  async function updateCart({ rowKey, action = null, quantity = null }) {
    try {
      const payload = action
        ? { action }
        : { quantity };
      const res = await fetch(`${updateUrl}/${rowKey}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN':     token,
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type':     'application/json',
          'Accept':           'application/json',
        },
        body: JSON.stringify(payload)
      });
      if (!res.ok) throw new Error('Network error');
      const data = await res.json();
      if (data.success) {
        // tìm lại dòng tương ứng
        const btnDec = document.querySelector(`.qty-btn[data-key="${rowKey}"].minus`);
        const row    = btnDec.closest('.d-flex.align-items-start');
        const inputEl = row.querySelector('.item-qty-input');

        // cập nhật input và dataset
        inputEl.value = data.quantity;
        const cb = row.querySelector('input.rowCheck');
        if (cb) cb.dataset.qty = data.quantity;

        // cập nhật biểu tượng nút giảm
        if (data.quantity > 1) {
          btnDec.innerHTML = '&minus;';
        } else {
          btnDec.innerHTML = '<i class="fa fa-trash"></i>';
        }

        // nếu = 0 thì remove luôn
        if (data.quantity === 0) {
          row.remove();
        }

        // và summary
        recalcSummary();
      }
    } catch (err) {
      console.error('Lỗi cập nhật giỏ hàng:', err);
    }
  }

  // 2.1) xử lý inc/dec qua nút bấm
  document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const rowKey = btn.dataset.key;
      const action = btn.dataset.action; // "inc" hoặc "dec"
      updateCart({ rowKey, action });
    });
  });

  // 2.2) xử lý khi blur hoặc Enter trên ô input
  document.querySelectorAll('.item-qty-input').forEach(input => {
    // khi mất focus, gửi giá trị mới
    input.addEventListener('blur', () => {
      const raw = parseInt(input.value);
      const newQty = isNaN(raw) || raw < 1 ? 1 : raw;
      const rowKey = input.closest('form').querySelector('.qty-btn').dataset.key;
      updateCart({ rowKey, quantity: newQty });
    });
    // khi bấm Enter, prevent default và trigger blur luôn
    input.addEventListener('keydown', e => {
      if (e.key === 'Enter') {
        e.preventDefault();
        input.blur();
      }
    });
  });

  // === 3) Xử lý nút favorite như cũ ===
  document.querySelectorAll('.btn-favorite').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      fetch(`/favorites/toggle/${id}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept':       'application/json',
        },
      })
      .then(res => res.json())
      .then(json => {
        const icon = btn.querySelector('i.fa-heart');
        if (json.added) {
          icon.classList.replace('far','fas');
        } else {
          icon.classList.replace('fas','far');
        }
      })
      .catch(console.error);
    });
  });

});
</script>
@endpush






