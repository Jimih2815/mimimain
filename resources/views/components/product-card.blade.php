<div class="card h-100">
  @if($product->img)
    <img src="{{ asset('storage/'.$product->img) }}"
         class="card-img-top"
         alt="{{ $product->name }}">
  @else
    <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
         style="height:200px">
      No Image
    </div>
  @endif

  <div class="card-body d-flex flex-column">
    <h5 class="card-title">{{ $product->name }}</h5>

    @if($product->description)
      <p class="card-text text-truncate flex-grow-1">
        {{ $product->description }}
      </p>
    @endif

    <p class="card-text fw-bold">
      {{ number_format($product->base_price,0,',','.') }}₫
    </p>

    <a href="{{ route('products.show', $product->slug) }}"
       class="btn btn-primary mt-auto">
      Xem chi tiết
    </a>
  </div>
</div>
