@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
    <h2 class="mb-3">Giỏ hàng</h2>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @elseif (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="checkout-form" action="{{ route('checkout.show') }}" method="GET">
        @csrf
        @if (count($cart))
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Thành tiền</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart as $key => $item)
                        <tr>
                            <td>
                                <input type="checkbox"
                                       name="selected_ids[]"
                                       value="{{ $key }}"
                                       class="rowCheck">
                            </td>
                            <td>
                                @if ($item['image'])
                                    <img src="{{ $item['image'] }}" width="60" alt="{{ $item['name'] }}">
                                @endif
                            </td>
                            <td>{{ $item['name'] }}</td>
                            <td class="text-end">{{ number_format($item['price'], 0, ',', '.') }}₫</td>
                            <td class="text-center">{{ $item['quantity'] }}</td>
                            <td class="text-end">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫</td>
                            <td>
                                <button type="submit"
                                        class="btn btn-danger btn-sm"
                                        formaction="{{ route('cart.remove', $key) }}"
                                        formmethod="POST"
                                        onclick="return confirm('Xoá {{ $item['name'] }}?')">
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-end mb-3">
                <h4>Tổng cộng: {{ number_format($total, 0, ',', '.') }}₫</h4>
                <button type="submit" class="btn btn-primary">Thanh toán</button>
            </div>
        @else
            <p>Giỏ hàng trống!</p>
        @endif
    </form>

    @push('scripts')
    <script>
        document.getElementById('checkAll')
            ?.addEventListener('change', e =>
                document.querySelectorAll('.rowCheck')
                        .forEach(c => c.checked = e.target.checked));
    </script>
    @endpush
@endsection
