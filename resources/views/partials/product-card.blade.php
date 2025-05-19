{{-- resources/views/partials/product-card.blade.php --}}
<div class="card h-100">
  <a href="{{ route('products.show', $product->slug) }}">
    <img
      src="{{ asset('storage/'.$product->img) }}"
      class="card-img-top"
      alt="{{ $product->name }}">
  </a>
  <div class="card-body d-flex flex-column">
    <h5 class="card-title ten-mobile">{{ $product->name }}</h5>
    <p class="card-text text-danger fw-bold">
      {{ number_format($product->base_price,0,',','.') }}₫
    </p>
    <!-- <a href="{{ route('products.show', $product->slug) }}"
       class="mt-auto btn btn-sm btn-outline-primary">
      Xem chi tiết
    </a> -->
  </div>
</div>
