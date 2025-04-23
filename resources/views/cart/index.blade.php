{{-- resources/views/cart/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
    <h2 class="mb-3">Giỏ hàng</h2>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @elseif (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ==== FORM THANH TOÁN (DUY NHẤT) ==== --}}
    <form id="checkout-form" action="{{ route('checkout.show') }}" method="GET">
    @csrf {{-- hidden _token cho *mọi* submit của form --}}

        @if (count($cart))
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px">
                            <input type="checkbox" id="checkAll">
                        </th>
                        <th style="width: 70px">Ảnh</th>
                        <th>Sản phẩm</th>
                        <th class="text-end" style="width: 120px">Đơn giá</th>
                        <th class="text-center" style="width: 90px">Số lượng</th>
                        <th class="text-end" style="width: 120px">Thành tiền</th>
                        <th style="width: 70px"></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($cart as $id => $item)
                        <tr>
                            {{-- checkbox nằm trong FORM checkout --}}
                            <td>
                                <input type="checkbox"
                                       name="selected_ids[]"  {{-- gửi lên cho checkout --}}
                                       value="{{ $id }}"
                                       class="rowCheck">
                            </td>

                            <td>
                                @if ($item['image'])
                                    <img src="{{ $item['image'] }}" width="60" alt="{{ $item['name'] }}">
                                @endif
                            </td>

                            <td>{{ $item['name'] }}</td>

                            <td class="text-end">
                                {{ number_format($item['price'], 0, ',', '.') }}₫
                            </td>

                            <td class="text-center">{{ $item['quantity'] }}</td>

                            <td class="text-end">
                                {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫
                            </td>

                            {{-- NÚT XÓA: KHÔNG lồng form, dùng `formaction`/`formmethod` --}}
                            <td>
                                <button type="submit"
                                        class="btn btn-danger btn-sm"
                                        formaction="{{ route('cart.remove', $id) }}"
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
                <h4>Tổng cộng:
                    {{ number_format($total, 0, ',', '.') }}₫
                </h4>

                <button type="submit" class="btn btn-primary">Thanh toán</button>

            </div>
        @else
            <p>Giỏ hàng trống!</p>
        @endif
    </form>

    @push('scripts')
        <script>
            // Check-all
            document.getElementById('checkAll')
                ?.addEventListener('change', e =>
                    document.querySelectorAll('.rowCheck')
                            .forEach(c => c.checked = e.target.checked));
        </script>
    @endpush
@endsection
