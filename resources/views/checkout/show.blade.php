{{-- resources/views/checkout/show.blade.php --}}
@extends('layouts.app')

@section('title','Thanh Toán')

@section('content')
<div class="trang-checkout flex-can-giua">
  <div class="checkout-cont row flex-can-giua">
    {{-- ==== Cột trái: Sản phẩm của bạn ==== --}}
    <div class="col-md-6 danh-sach-san-pham">
      <h4 class="mb-3">Sản phẩm của bạn</h4>
      @php $grand = 0; @endphp

      @foreach($items as $item)
      @php
        // tính đúng: gồm extra_price nếu có
        $unitPrice = $item['price'] + ($item['extra_price'] ?? 0);
        $line      = $unitPrice * $item['quantity'];
        $grand    += $line;
      @endphp

        <div class="d-flex align-items-start mb-4">
          {{-- Ảnh sản phẩm --}}
          <div class="text-center">
            @if(!empty($item['image']))
              <img src="{{ asset('storage/'.$item['image']) }}"
                   class="img-fluid rounded"
                   style="width:120px; height:auto;"
                   alt="{{ $item['name'] }}">
            @endif
          </div>

          {{-- Thông tin & số lượng --}}
          <div class="ms-3 flex-grow-1">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-1">
                {{ $item['name'] }}
                <span style="color:#b83232; font-weight:bold; font-size:1.1em;">
                  x{{ $item['quantity'] }}
                </span>
              </h5>
              @php /* sử dụng đơn giá đã bao extra */ @endphp
              <span class="fw-bold">
                {{ number_format($unitPrice,0,',','.') }}₫
              </span>
            </div>

            @php
              $vals = \App\Models\OptionValue::whereIn('id', $item['options'] ?? [])
                        ->with('type')->get();
            @endphp
            @foreach($vals as $val)
              <div class="text-muted">
                <strong>{{ $val->type->name }}:</strong> {{ $val->value }}
              </div>
            @endforeach
          </div>
        </div>
        <hr>
      @endforeach

      @php
        // Tính phí ship:
        $shipping = $grand > 199000
                    ? 0
                    : ($grand === 0 ? 0 : 20000);
        // Tổng cộng = thành tiền + phí ship
        $grandTotal = $grand + $shipping;
      @endphp

      <div class="text-end">
        {{-- Thành Tiền --}}
        <p class="d-flex justify-content-between">
          <span>Thành Tiền</span>
          <strong>{{ number_format($grand,0,',','.') }}₫</strong>
        </p>

        {{-- Phí Ship --}}
        <p class="d-flex justify-content-between">
          <span>Phí Ship</span>
          <strong>
            {{ $shipping === 0 && $grand > 0 
               ? 'Free' 
               : number_format($shipping,0,',','.') . '₫' 
            }}
          </strong>
        </p>

        <hr>

        {{-- Tổng Cộng --}}
        <h5 class="fw-bold">
          Tổng Cộng: {{ number_format($grandTotal,0,',','.') }}₫
        </h5>
      </div>
    </div>

    {{-- ==== Cột phải: Thông tin người nhận + Phương thức thanh toán ==== --}}
    <div class="col-md-6 thong-tin-nguoi-nhan">
      <h4 class="mb-3">Thông tin người nhận</h4>
      <form id="checkoutForm" action="{{ route('checkout.confirm') }}" method="POST">
        @csrf

        {{-- Thông báo validate --}}
        <p id="checkout-validate-warning"
           style="display:none; color:#b83232; margin-bottom:8px; font-size:1.5rem;">
          Vui lòng nhập các ô bắt buộc mà chưa nhập
        </p>

        <div class="mb-3">
          <label class="form-label">Họ tên</label>
          <input type="text" name="fullname" class="form-checkout" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Số điện thoại</label>
          <input type="text" name="phone" class="form-checkout" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Địa chỉ</label>
          <textarea name="address" class="form-checkout" rows="2" required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Ghi chú</label>
          <textarea name="note" class="form-checkout" rows="2"></textarea>
        </div>

        <h4 class="mb-3">Phương thức thanh toán</h4>
        <div class="form-checkout-thanh-toan mb-2">
          <input class="form-checkout-input" type="radio"
                 name="payment" id="cod" value="cod" checked>
          <label class="form-check-label" for="cod">
            Thanh toán khi nhận hàng (COD)
          </label>
        </div>
        <div class="form-checkout-thanh-toan mb-4">
          <input class="form-checkout-input" type="radio"
                 name="payment" id="bank" value="bank">
          <label class="form-check-label" for="bank">
            Chuyển khoản ngân hàng
          </label>
        </div>

        {{-- Nút COD --}}
        <button type="submit"
                class="btn btn-success mb-4 nut-thanh-toan"
                id="confirmCod">
          Xác nhận thanh toán
        </button>

        {{-- Box CK (ẩn mặc định) --}}
        <div id="bankSection" style="display:none;">
          <h5>Chuyển khoản ngân hàng</h5>
          <img src="{{ $qrUrl }}" alt="QR Code" class="mb-3">
          <p>
            Chủ TK: <strong>PHAN THAO NGUYEN</strong><br>
            Số TK: <strong>19032724004016</strong><br>
            Ngân hàng: <strong>Techcombank</strong><br>
            Số tiền: <strong>{{ number_format($grandTotal,0,',','.') }}₫</strong>
          </p>
          <p class="mb-3">
            Nội dung CK: <code>{{ $bankRef }}</code>
          </p>
          <p class="text-canh-bao mb-3">
            LƯU Ý: KHÔNG THAY ĐỔI NỘI DUNG CHUYỂN KHOẢN
          </p>
          <p class="text-luu-y mb-3">
            Sau khi chuyển khoản xong, ấn “Xác nhận đã chuyển khoản” nhé!
          </p>

          <button type="button"
                  id="confirmBank"
                  class="btn mb-4 nut-thanh-toan">
            Xác nhận đã chuyển khoản
          </button>
        </div>

        <input type="hidden" name="bank_ref" value="{{ $bankRef }}">
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const codRadio    = document.getElementById('cod');
    const bankRadio   = document.getElementById('bank');
    const bankSection = document.getElementById('bankSection');
    const confirmCod  = document.getElementById('confirmCod');
    const confirmBank = document.getElementById('confirmBank');
    const form        = document.getElementById('checkoutForm');
    const warnEl      = document.getElementById('checkout-validate-warning');

    function validate() {
      const fields = [
        { name: 'fullname', label: 'Họ tên' },
        { name: 'phone',    label: 'Số điện thoại' },
        { name: 'address',  label: 'Địa chỉ' },
      ];
      const missing = fields
        .filter(f => ! form.querySelector(`[name="${f.name}"]`).value.trim())
        .map(f => f.label);
        if (missing.length) {
          warnEl.textContent = '!!! Vui lòng nhập: ' + missing.join(', ');
          warnEl.style.display = 'block';
          // >>> Cuộn nhẹ nhàng xuống chỗ cảnh báo để user khỏi nhảy nhót tìm
          warnEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
          return false;
        }
      warnEl.style.display = 'none';
      return true;
    }

    confirmCod.addEventListener('click', function(e) {
      if (!validate()) e.preventDefault();
    });

    confirmBank.addEventListener('click', function() {
      if (validate()) form.submit();
    });

    codRadio.addEventListener('change', () => {
      bankSection.style.display = 'none';
      confirmCod.style.display  = 'inline-block';
    });
    bankRadio.addEventListener('change', () => {
      bankSection.style.display = 'block';
      confirmCod.style.display  = 'none';
    });
  });
</script>
@endpush
