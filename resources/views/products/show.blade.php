@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-md-6">
      <img src="{{ $product->img }}" alt="{{ $product->name }}" class="img-fluid mb-3">
    </div>
    <div class="col-md-6">
      <h1>{{ $product->name }}</h1>
      <p>{{ $product->description }}</p>
      <h4>Giá gốc: {{ number_format($product->base_price, 0, ',', '.') }}₫</h4>

      <form action="{{ route('cart.add', ['id' => $product->id]) }}" method="POST">
        @csrf
        @foreach($optionTypes as $type)
          <div class="mb-3">
            <label for="option_{{ $type->id }}" class="form-label">{{ $type->name }}</label>
            <select name="options[{{ $type->id }}]" id="option_{{ $type->id }}" class="form-select">
              @foreach($type->values as $val)
                <option value="{{ $val->id }}" data-extra="{{ $val->extra_price }}">
                  {{ $val->value }} (+{{ number_format($val->extra_price, 0, ',', '.') }}₫)
                </option>
              @endforeach
            </select>
          </div>
        @endforeach

        <button type="submit" class="btn btn-primary">Thêm vào giỏ hàng</button>
      </form>
    </div>
  </div>
</div>
@endsection
