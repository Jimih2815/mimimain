@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">🛒 Giỏ hàng của bạn</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(count($cart) > 0)
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $id => $item)
                <tr>
                    <td>
                        @if($item['image'])
                            <img src="{{ $item['image'] }}" alt="" width="60">
                        @else
                            —
                        @endif
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

        <div class="text-right">
            <h4>Tổng cộng: <strong>{{ number_format($total, 0, ',', '.') }}₫</strong></h4>
        </div>
    @else
        <p>Giỏ hàng trống. Hãy thêm sản phẩm nào! 😊</p>
    @endif
</div>
@endsection
