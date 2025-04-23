{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="row">
    {{-- Ảnh lớn --}}
    <div class="col-md-6">
      @if($product->img)
        <img 
          src="{{ asset('storage/'.$product->img) }}" 
          class="img-fluid" alt="{{ $product->name }}">
      @else
        <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
             style="height:400px">
          No Image
        </div>
      @endif
    </div>

    {{-- Thông tin & chọn Option --}}
    <div class="col-md-6">
      <h2>{{ $product->name }}</h2>
      <p class="text-muted">{{ $product->description }}</p>
      <p class="fs-4">Giá gốc: <strong>{{ number_format($product->base_price,0,',','.') }}₫</strong></p>

      <form action="{{ route('cart.add', $product->id) }}" method="POST">
        @csrf

        {{-- Loop qua từng OptionType riêng của product --}}
        @forelse($optionTypes as $type)
          <div class="mb-3">
            <label class="form-label">{{ $type->name }}</label>
            <select name="options[{{ $type->id }}]" class="form-select" required>
              <option value="">Chọn {{ $type->name }}…</option>
              @foreach($type->values as $val)
                <option value="{{ $val->id }}">
                  {{ $val->value }}
                  @if($val->extra_price)
                    (+{{ number_format($val->extra_price,0,',','.') }}₫)
                  @endif
                </option>
              @endforeach
            </select>
          </div>
        @empty
          <p class="text-warning">Sản phẩm này không có tuỳ chọn bổ sung.</p>
        @endforelse

        <button class="btn btn-primary">Thêm vào giỏ hàng</button>
      </form>
    </div>
  </div>
</div>
@endsection
