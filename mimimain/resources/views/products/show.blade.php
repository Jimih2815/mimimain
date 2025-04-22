@extends('layouts.app')

@section('title', $classification->name)

@section('content')
<div class="container py-5">
  <div class="row">
    {{-- Gallery --}}
    <div class="col-md-6">
      <div class="d-flex mb-3">
        <div class="me-3" style="width:80px;">
          @foreach($subImages as $img)
            <img
              src="{{ asset('storage/'.$img) }}"
              class="img-fluid mb-2 thumbnail"
              style="cursor:pointer;"
              onclick="document.getElementById('main-img').src=this.src"
            >
          @endforeach
        </div>
        <img
          id="main-img"
          src="{{ asset('storage/'.$mainImage) }}"
          class="img-fluid"
          style="max-height:400px; object-fit:cover;"
        >
      </div>

      {{-- Size options --}}
      <div class="mb-4">
        <h5>Chọn kích cỡ:</h5>
        <div class="btn-group" role="group">
          @foreach($sizeOptions as $opt)
            <button
              type="button"
              class="btn btn-outline-secondary size-option"
              data-price="{{ $opt->extra_price }}"
            >
              {{ $opt->name }}
            </button>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Info --}}
    <div class="col-md-6">
      <h2 class="mb-3">{{ $classification->name }}</h2>
      @php
        $base    = $classification->product->base_price;
        $initial = $base + ($sizeOptions->first()->extra_price ?? 0);
      @endphp
      <p id="price" class="h4 text-primary">{{ number_format($initial,0,',','.') }}₫</p>

      {{-- Nút Thêm giỏ hàng --}}
      <form action="{{ route('cart.add', ['id' => $classification->id]) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary w-100 mb-2">
          Thêm vào giỏ hàng
        </button>
      </form>

      {{-- Nút Yêu thích --}}
      <button class="btn btn-outline-secondary w-100">
        ❤️ Yêu thích
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (function(){
    const base = {{ $classification->product->base_price }};
    document.querySelectorAll('.size-option').forEach(btn=>{
      btn.addEventListener('click',()=>{
        document.querySelectorAll('.size-option').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        const extra = parseInt(btn.dataset.price);
        document.getElementById('price').innerText =
          new Intl.NumberFormat('vi-VN').format(base+extra)+'₫';
      });
    });
  })();
</script>
@endpush
