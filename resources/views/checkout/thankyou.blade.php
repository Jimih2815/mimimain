{{-- resources/views/checkout/thankyou.blade.php --}}
@extends('layouts.app')
@section('title','Đặt hàng thành công')

@section('content')
<div class="fex-can-giua bat-col">
  <div class="anh-cam-on-mua-hang-cont">
    <img src="{{ asset('hinhanh/anh-cam-on.png') }}" 
            alt="Cảm ơn bạn đã mua hàng" 
            class="img-cam-on mb-4" 
            style="max-width: 100%; height: auto;">
  </div>
  <div class="py-5 text-center trang-cam-on">


  <!-- <h2 class="mb-3">Cảm ơn bạn đã đặt hàng!</h2> -->
  <p>Mã đơn: <strong>#{{ $order->order_code }}</strong></p>
  <p>Tổng tiền: <strong>{{ number_format($order->total,0,',','.') }}₫</strong></p>

  @if($order->payment_method=='cod')
    <div class="alert alert-info mt-4">
      Bạn sẽ thanh toán tiền mặt khi nhận hàng.
    </div>
  @else
    <div class="mt-4 box-phuong-thuc">
      <p><strong>Phương thức:</strong> Chuyển khoản ngân hàng</p>
      <p>Nội dung CK: <code>{{ $order->bank_ref }}</code></p>
    </div>
  @endif

  <div class="text-center mt-4 d-flex gap-3 justify-content-center align-items-center">
  <a href="{{ route('home') }}" class="btn-mimi nut-vang text-decoration-none">
    Tiếp tục mua hàng
  </a>
  <a href="{{ route('profile.edit') }}#orders" class="btn-mimi nut-xanh text-decoration-none">
  Theo dõi đơn hàng
</a>

</div>

  </div>
</div>
@endsection
