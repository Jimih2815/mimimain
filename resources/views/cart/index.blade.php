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
            {{-- Checkbox chọn sản phẩm, liên kết tới form thanh toán --}}
            <div class="me-3">
              <input type="checkbox"
                     name="selected_ids[]"
                     value="{{ $key }}"
                     form="checkout-form"
                     class="rowCheck">
            </div>

            {{-- Ảnh + nút tăng/giảm --}}
            <div class="text-center">
              <img src="{{ asset('storage/'.$item['image']) }}"
                   class="img-fluid rounded anh-san-pham"
                   alt="{{ $item['name'] }}">

              <div class="d-flex tang-giam-va-tim">
                {{-- Giảm hoặc xóa --}}
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

                    {{-- Số lượng --}}
                    <span class="btn btn-outline-secondary btn-sm mx-1 disabled xoa-border">
                    {{ $item['quantity'] }}
                    </span>

                    {{-- Tăng --}}
                    <form action="{{ route('cart.update', $key) }}"
                        method="POST"
                        class="d-inline-block xoa-border">
                    @csrf
                    <button type="submit"
                            name="action" value="inc"
                            class="btn btn-outline-secondary btn-sm  xoa-border">
                        +
                    </button>
                    </form>
                </div>
                {{-- Yêu thích --}}
                <button type="button"
                        class="trai-tim">
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
                    <p class="d-flex justify-content-between">
                    <span>Thành Tiền</span>
                    <span>{{ number_format($total,0,',','.') }}₫</span>
                    </p>
                    <p class="d-flex justify-content-between">
                    <span>Phí Ship</span>
                    <span>Free</span>
                    </p>
                    <hr>
                    <p class="d-flex justify-content-between fw-bold">
                    <span>Tổng Cộng</span>
                    <span>{{ number_format($total,0,',','.') }}₫</span>
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
  const form   = document.getElementById('checkout-form');
  const btn    = document.getElementById('checkout-button');
  const warnEl = document.getElementById('checkout-warning');

  form.addEventListener('submit', e => {
    // đếm số checkbox đã check
    const checked = document.querySelectorAll('input.rowCheck:checked').length;
    if (checked === 0) {
      e.preventDefault();
      // show warning
      warnEl.style.display = 'block';
      // viền đỏ cho nút
      btn.style.border = '1px solid #3a9b98';
    }
  });
</script>
@endpush
