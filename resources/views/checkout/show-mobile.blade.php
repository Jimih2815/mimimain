{{-- resources/views/checkout/show-mobile.blade.php --}}
@extends('layouts.app-mobile')

@section('title', 'Thanh Toán')

@section('content')
<style>
  #paymentSummary {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 0;
  }
  .trang-checkout-mobile {
  /* height: 100vh;  BỎ đi */
    min-height: 100vh;         
    display: flex;             
    flex-direction: column;
  }
  #paymentSummary {
    height: 10rem;
  }
  .danh-sach-san-pham {
    padding: 1rem 1rem 12rem 1rem;
     flex: 1 0 auto;             
  overflow: visible;
  }

  .dien-thong-tin {
    min-height: 100vh;
  }
  .tong-cong-cont {
    border: 2px solid #4ab3af;
    border-radius: 5px;
    height: 100%;
    margin: 0px 5px;
    padding: 10px;
    background-color: white;
  }
  .tong-cong {
    width: 100%;
  }
  #checkout-btn-mobile {
    width: 100%;
    font-size: 1rem;
    font-weight: 900;
  }
  .anh-qr {
    width: 100%;
  }
  .text-canh-bao {
    color: #fe3b27;
    font-weight: 600;
    font-size: 0.9rem;
  }
  .text-luu-y {
     color: #4ab3af;
        font-size: 0.9rem;
        font-weight: 600;
  }
  .nut-thanh-toan {
    width: 100%;
    margin: 0 auto;
    display: block;
    border-radius: 50px;
    background-color: #4ab3af;
    font-size: 1.2rem;
    color: white;
    padding: 10px 15px;
    font-weight: 800;
    }
    
    .form-check-input {
  width: 20px;
  height: 20px;
  appearance: none;
  border: 2px solid #4ab3af;
  border-radius: 50%;
  cursor: pointer;
  position: relative;
  transition: all 0.2s ease;
}

.form-check-input:hover {
  box-shadow: 0 0 5px rgba(74, 179, 175, 0.6);
}

.form-check-input:checked::after {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  width: 10px;
  height: 10px;
  background-color: #4ab3af;
  border-radius: 50%;
  transform: translate(-50%, -50%);
}

.form-check-input:checked + .form-check-label {
  color: #4ab3af;
  font-weight: 600;

}
.form-check-input:checked {
  background-color: transparent;
  border-color: #4ab3af;
}
.form-check-label {
  padding-top: 0.2rem;
    padding-left: 10px;
}
.tieu-de {
  color: #fe3b27;
  font-weight: 800;
}
.form-checkout {
    border-bottom: 2px solid #d1a029;
    border-left: 0px;
    border-right: 0px;
    border-top: 0px;
    border-radius: 0px;
    background-color: transparent;
    width: 100%;
}
.form-checkout:focus {
            outline: none; /* Xoá cái viền đen xấu xí */
            border-color: #4ab3af; /* Thay bằng màu đẹp nhẹ nhàng */
            box-shadow: 0 0 0 3px rgba(74, 179, 175, 0.25); /* Thêm glow nhẹ nếu thích */
        }
.san-pham-cont img {
  width: 30%;
    aspect-ratio: 1 / 1;
    height: auto;
    object-fit: cover;
    overflow: hidden;
  
}
.san-pham-cont .mo-ta-san-pham {
  width: 40%;
}
.san-pham-cont .gia-tien {
width: 30%;
}
</style>


<div class="trang-checkout-mobile ">
  {{-- Danh sách sản phẩm xếp dọc --}}
  <div class="d-flex flex-column danh-sach-san-pham">
    <h4 class="mb-3 mt-3 tieu-de">Sản phẩm của bạn</h4>
    @foreach($items as $item)
      @php
        $extra        = $item['extra_price'] ?? 0;
        $unitPrice    = $item['price'] + $extra;
        $line         = $unitPrice * $item['quantity'];
        $optionValues = \App\Models\OptionValue::whereIn('id', $item['options'] ?? [])
                          ->with('type')
                          ->get();
      @endphp

      <div class="mb-3 p-2 rounded d-flex justify-content-between align-items-start border-0 san-pham-cont gap-2">
        {{-- Phần trái: ảnh + tên + các thuộc tính --}}
          @if(!empty($item['image']))
            <img src="{{ asset('storage/'.$item['image']) }}"
                width="60" height="60" class="mb-2 rounded">
          @endif
        <div class="d-flex flex-column align-items-start mo-ta-san-pham">


          <span>{{ $item['name'] }} <h5>x {{ $item['quantity'] }}</h5></span>

          @foreach($optionValues as $val)
            <span class="text-muted small mt-1">
              <strong>{{ $val->type->name }}:</strong> {{ $val->value }}
            </span>
          @endforeach
        </div>

        {{-- Phần phải: giá tiền --}}
        <span class="fw-bold gia-tien d-flex justify-content-center">{{ number_format($line,0,',','.') }}₫</span>
      </div>
    @endforeach

  </div>

  {{-- Fixed-bottom summary + button --}}
  <div id="paymentSummary" class="fixed-bottom bg-transparent">
    <div class="d-flex flex-column justify-content-between align-items-center tong-cong-cont">
      <div class="text-end tong-cong">
        <p class="mb-1 d-flex justify-content-between">
          <span>Thành Tiền</span>
          <strong>{{ number_format($grand,0,',','.') }}₫</strong>
        </p>
        <p class="mb-1 d-flex justify-content-between">
          <span>Phí Ship</span>
          <strong>
            {{ ($shipping === 0 && $grand > 0)
              ? 'Free'
              : number_format($shipping,0,',','.') . '₫'
            }}
          </strong>
        </p>
        <hr class="my-1">
        <p class="mb-0 d-flex justify-content-between">
          <span>Tổng Cộng</span>
          <strong>{{ number_format($grand + $shipping,0,',','.') }}₫</strong>
        </p>
      </div>
      <button id="checkout-btn-mobile" class="btn-mimi nut-xanh">Thanh toán</button>
    </div>
  </div>

{{-- Offcanvas slide-up thông tin người nhận --}}
<div class="offcanvas offcanvas-bottom dien-thong-tin" tabindex="-1" id="offcanvasRecipient">
  <div class="offcanvas-header d-flex justify-content-between align-items-center">
    <h5 class="offcanvas-title tieu-de">Thông tin người nhận</h5>
    <button type="button" class="border-0 bg-transparent d-flex justify-content-center align-items-center" data-bs-dismiss="offcanvas"> <i class="fa fa-times fa-lg"></i></button>
  </div>
  <div class="offcanvas-body">
    <form id="checkoutForm" action="{{ route('checkout.confirm') }}" method="POST">
      @csrf

      <!-- <h4 class="mb-3">Thông tin người nhận</h4> -->
      <p id="checkout-validate-warning"
         style="display:none; color:#b83232; margin-bottom:8px; font-size:1.2rem;"></p>

      <div class="mb-3">
        <label class="form-label">Họ tên</label>
        <input type="text" name="fullname" class="form-checkout form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Số điện thoại</label>
        <input
          type="tel"
          name="phone"
          class="form-checkout form-control"
          required
          inputmode="numeric"
          pattern="[0-9]{9,11}"
          minlength="9"
          maxlength="11"
        >
      </div>
      <div class="mb-3">
        <label class="form-label">Địa chỉ</label>
        <textarea name="address" class="form-checkout form-control" rows="2" required></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Ghi chú</label>
        <textarea name="note" class="form-checkout form-control" rows="2"></textarea>
      </div>

      <h4 class="mb-3">Phương thức thanh toán</h4>
      <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="payment" id="cod" value="cod" checked>
        <label class="form-check-label" for="cod">
          Thanh toán khi nhận hàng (COD)
        </label>
      </div>
      <div class="form-check mb-4">
        <input class="form-check-input" type="radio" name="payment" id="bank" value="bank">
        <label class="form-check-label" for="bank">
          Chuyển khoản ngân hàng
        </label>
      </div>

      {{-- Nút COD --}}
      <button type="submit"
              class="btn btn-success mb-3 nut-thanh-toan"
              id="confirmCod">
        Xác nhận thanh toán
      </button>

      {{-- Box CK (ẩn mặc định) --}}
      <div id="bankSection" style="display:none;">
        <h5>Chuyển khoản ngân hàng</h5>
        <img src="{{ $qrUrl }}" alt="QR Code" class="mb-3 anh-qr">
        <p>
          Chủ TK: <strong>PHAN THAO NGUYEN</strong><br>
          Số TK: <strong>19032724004016</strong><br>
          Ngân hàng: <strong>Techcombank</strong><br>
          Số tiền: <strong>{{ number_format($grand + $shipping,0,',','.') }}₫</strong>
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
                class="mb-4 nut-thanh-toan border-0">
          Xác nhận đã chuyển khoản
        </button>
      </div>

      <input type="hidden" name="bank_ref" value="{{ $bankRef }}">
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // 1) Offcanvas
  const offcanvas = new bootstrap.Offcanvas('#offcanvasRecipient');
  document.getElementById('checkout-btn-mobile')
          .addEventListener('click', () => offcanvas.show());

  // 2) Form & validate & toggle COD/Bank
  const form        = document.getElementById('checkoutForm');
  const warnEl      = document.getElementById('checkout-validate-warning');
  const codRadio    = document.getElementById('cod');
  const bankRadio   = document.getElementById('bank');
  const bankSection = document.getElementById('bankSection');
  const confirmCod  = document.getElementById('confirmCod');
  const confirmBank = document.getElementById('confirmBank');

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
      warnEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return false;
    }
    const phoneVal = form.querySelector('[name="phone"]').value.trim();
    if (!/^[0-9]{9,11}$/.test(phoneVal)) {
      warnEl.textContent = '!!! Số điện thoại phải là 9–11 chữ số.';
      warnEl.style.display = 'block';
      form.querySelector('[name="phone"]').focus();
      return false;
    }
    warnEl.style.display = 'none';
    return true;
  }

  confirmCod.addEventListener('click', e => { if (!validate()) e.preventDefault() });
  confirmBank.addEventListener('click', () => { if (validate()) form.submit() });

  codRadio.addEventListener('change', () => {
    bankSection.style.display = 'none';
    confirmCod.style.display  = 'inline-block';
  });
  bankRadio.addEventListener('change', () => {
    bankSection.style.display = 'block';
    confirmCod.style.display  = 'none';
  });

  if ('{{ old("payment") }}' === 'bank') {
    bankRadio.checked         = true;
    bankSection.style.display = 'block';
    confirmCod.style.display  = 'none';
  }

  // 3) Dừng paymentSummary khi chạm footer
  const payDiv   = document.getElementById('paymentSummary');
  const footer   = document.getElementById('siteFooter');
  const payH     = payDiv.offsetHeight;                 // cao của thanh
  const GAP      = parseFloat(getComputedStyle(document.documentElement)
                  .fontSize) * 2;                       // 2rem → px
  const limit    = footer.offsetTop;                    // top của footer

  function pinUntilFooter() {
    const payBottomFixed = window.scrollY + window.innerHeight; // mép dưới giả định
    const stopLine       = limit - GAP;                         // “vạch đích” (cách 2rem)

    if (payBottomFixed >= stopLine) {
      // Chạm vạch đích: chuyển sang absolute và ghim cách 2rem
      payDiv.style.position = 'absolute';
      payDiv.style.top      = (stopLine - payH) + 'px';
      payDiv.style.bottom   = 'auto';
    } else {
      // Chưa chạm: giữ fixed-bottom
      payDiv.style.position = 'fixed';
      payDiv.style.top      = 'auto';
      payDiv.style.bottom   = '0';
    }
  }

  pinUntilFooter();
  document.addEventListener('scroll', pinUntilFooter);
});
</script>
@endpush


