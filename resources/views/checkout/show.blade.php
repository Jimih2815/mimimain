@extends('layouts.app')

@section('title','Thanh Toán')

@section('content')
<div class="container py-4">
  <h2 class="mb-4">Thanh toán đơn hàng</h2>

  {{-- Tầng 1: Sản phẩm --}}
  <div class="mb-5">
    <h4>Sản phẩm của bạn</h4>
    <table class="table">
      <thead>
        <tr>
          <th>Ảnh</th>
          <th>Sản phẩm</th>
          <th>Đơn giá</th>
          <th>Số lượng</th>
          <th>Thành tiền</th>
        </tr>
      </thead>
      <tbody>
        @php $grand = 0; @endphp
        @foreach($items as $item)
          @php
            $line   = $item['price'] * $item['quantity'];
            $grand += $line;
          @endphp
          <tr>
            <td>
              @if($item['image'])
                <img src="{{ $item['image'] }}" width="60" alt="">
              @endif
            </td>
            <td>{{ $item['name'] }}</td>
            <td>{{ number_format($item['price'],0,',','.') }}₫</td>
            <td>{{ $item['quantity'] }}</td>
            <td>{{ number_format($line,0,',','.') }}₫</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="text-end">
      <h5>Tổng: <strong>{{ number_format($grand,0,',','.') }}₫</strong></h5>
    </div>
  </div>

  {{-- Tầng 2 & 3: Form thông tin + chọn phương thức --}}
  <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
    @csrf

    {{-- Thông tin người nhận --}}
    <h4 class="mb-3">Thông tin người nhận</h4>
    <div class="row mb-4">
      <div class="col-md-6 mb-3">
        <label class="form-label">Họ tên</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Số điện thoại</label>
        <input type="text" name="phone" class="form-control" required>
      </div>
      <div class="col-12 mb-3">
        <label class="form-label">Địa chỉ</label>
        <textarea name="address" class="form-control" rows="2" required></textarea>
      </div>
      <div class="col-12 mb-4">
        <label class="form-label">Ghi chú</label>
        <textarea name="note" class="form-control" rows="2"></textarea>
      </div>
    </div>

    {{-- Chọn phương thức thanh toán --}}
    <h4 class="mb-3">Phương thức thanh toán</h4>
    <div class="form-check mb-2">
      <input
        class="form-check-input"
        type="radio"
        name="payment"
        id="cod"
        value="cod"
        checked
      >
      <label class="form-check-label" for="cod">COD (Thanh toán khi nhận hàng)</label>
    </div>
    <div class="form-check mb-4">
      <input
        class="form-check-input"
        type="radio"
        name="payment"
        id="bank"
        value="bank"
      >
      <label class="form-check-label" for="bank">Chuyển khoản ngân hàng</label>
    </div>

    {{-- Nút submit COD --}}
    <button type="submit" class="btn btn-success mb-4" id="confirmCod">Xác nhận thanh toán</button>

    {{-- Phần QR khi chọn chuyển khoản --}}
    <div id="bankSection" style="display:none;">
      <h5>Chuyển khoản ngân hàng</h5>

      {{-- QR Code EMVCo --}}
      <img src="{{ $qrUrl }}" alt="QR Code" class="mb-3">

      <p>
        Chủ TK: <strong>PHAN THAO NGUYEN</strong><br>
        Số TK: <strong>19032724004016</strong><br>
        Ngân hàng: <strong>Techcombank</strong><br>
        Số tiền: <strong>{{ number_format($grand,0,',','.') }}₫</strong>
      </p>
      <p class="text-warning">
        Bạn chuyển khoản thành công sau đó ấn 
        <strong>"Xác nhận đã chuyển khoản"</strong> nha
      </p>
      <button type="button" id="confirmBank" class="btn btn-primary">
        Xác nhận đã chuyển khoản
      </button>
    </div>
  </form>
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

    // Toggle giữa COD và Bank
    codRadio.addEventListener('change', () => {
      bankSection.style.display = 'none';
      confirmCod.style.display   = 'inline-block';
    });
    bankRadio.addEventListener('change', () => {
      bankSection.style.display = 'block';
      confirmCod.style.display   = 'none';
    });

    // Khi bấm “Xác nhận đã chuyển khoản” thì submit form
    confirmBank.addEventListener('click', () => form.submit());
  });
</script>
@endpush
