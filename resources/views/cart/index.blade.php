@extends('layouts.app')

@section('title','Giỏ hàng')

@section('content')
<div class="container py-4">
  <h2>Giỏ hàng</h2>

  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @elseif(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('checkout.show') }}" method="POST">
    @csrf

    @if(count($cart) > 0)
      <table class="table">
        <thead>
          <tr>
            <th><input type="checkbox" id="checkAll"></th>
            <th>Ảnh</th>
            <th>Sản phẩm</th>
            <th>Đơn giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($cart as $id => $item)
            <tr>
              <td>
                <input type="checkbox" name="selected_ids[]" value="{{ $id }}" class="rowCheck">
              </td>
              <td>@if($item['image'])<img src="{{ $item['image'] }}" width="60">@endif</td>
              <td>{{ $item['name'] }}</td>
              <td>{{ number_format($item['price'],0,',','.') }}₫</td>
              <td>{{ $item['quantity'] }}</td>
              <td>{{ number_format($item['price'] * $item['quantity'],0,',','.') }}₫</td>
              <td>
                <form action="{{ route('cart.remove',['id'=>$id]) }}" method="POST">
                  @csrf
                  <button class="btn btn-danger btn-sm">Xóa</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="text-end mb-4">
        <h4>Tổng cộng: {{ number_format($total,0,',','.') }}₫</h4>
      </div>

      <button class="btn btn-primary">Thanh toán</button>
    @else
      <p>Giỏ hàng trống!</p>
    @endif
  </form>
</div>

@push('scripts')
<script>
  // Check/Uncheck all
  document.getElementById('checkAll').addEventListener('change', function(){
    document.querySelectorAll('.rowCheck').forEach(ch=> ch.checked = this.checked);
  });
</script>
@endpush
@endsection
