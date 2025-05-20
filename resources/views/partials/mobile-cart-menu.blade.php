@php
  // Lấy giỏ hàng và tổng số lượng
  $cart  = session('cart', []);
  $count = array_sum(array_column($cart, 'quantity'));
@endphp

{{-- Giới hạn hiển thị tối đa 5 item, mỗi item ~2.5rem, tổng ~12.5rem (200px) --}}
<div style="max-height:200px; overflow-y:auto;">
  <ul class="list-unstyled m-0 p-0">
    @forelse($cart as $item)
      <li class="d-flex align-items-center mb-2">
        <img src="{{ asset('storage/'.$item['image']) }}"
             style="width:40px;height:40px;object-fit:cover;border-radius:4px"
             class="me-2">
        <span>{{ $item['name'] }} x{{ $item['quantity'] }}</span>
      </li>
    @empty
      <p class="text-center mb-0">Giỏ hàng trống</p>
    @endforelse
  </ul>
</div>

<div class="mt-3 text-center">
  <a href="{{ route('cart.index') }}"
     class="btn-mimi nut-vang mx-auto nut-gio-hang">Xem giỏ hàng</a>
</div>
