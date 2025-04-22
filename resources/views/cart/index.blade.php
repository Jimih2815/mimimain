@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container py-4">
  <h2>Giỏ hàng</h2>
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(count($cart) > 0)
    <table class="table">
      <thead>
        <tr>
          <th>Ảnh</th><th>Sản phẩm</th><th>Đơn giá</th>
          <th>Số lượng</th><th>Thành tiền</th><th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($cart as $id => $item)
          <tr>
            <td>
              @if($item['image'])<img src="{{ $item['image'] }}" width="60">@endif
            </td>
            <td>{{ $item['name'] }}</td>
            <td>{{ number_format($item['price'], 0, ',', '.') }}₫</td>
            <td>{{ $item['quantity'] }}</td>
            <td>{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫</td>
            <td>
              <form action="{{ route('cart.remove', ['id' => $id]) }}" method="POST">
                @csrf
                <button class="btn btn-danger btn-sm">Xóa</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="text-end">
      <h4>Tổng cộng: {{ number_format($total, 0, ',', '.') }}₫</h4>
    </div>
  @else
    <p>Giỏ hàng trống!</p>
  @endif
</div>
@endsection
