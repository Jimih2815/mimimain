{{-- resources/views/checkout/thankyou.blade.php --}}
@extends('layouts.app')
@section('title','Đặt hàng thành công')

@section('content')
<div class="py-5 text-center trang-cam-on">
  <h2 class="mb-3">Cảm ơn bạn đã đặt hàng!</h2>
  <p>Mã đơn: <strong>#{{ $order->id }}</strong></p>
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

  <a href="{{ route('home') }}" class="btn mt-4 nut-tiep-tuc">
    Tiếp tục mua hàng
  </a>
</div>
@endsection
