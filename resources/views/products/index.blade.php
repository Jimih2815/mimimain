{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="mb-4">Danh sách Sản phẩm</h1>

  {{-- Search --}}
  <form class="row g-2 mb-4">
    <div class="col-auto">
      <input 
        type="text" 
        name="q" 
        value="{{ request('q') }}" 
        class="form-control" 
        placeholder="Tìm kiếm...">
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-secondary">Search</button>
    </div>
  </form>

  <div class="row">
    @forelse($products as $product)
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          @if($product->img)
            <img 
              src="{{ asset('storage/'.$product->img) }}" 
              class="card-img-top" 
              style="object-fit:cover; height:200px;" 
              alt="{{ $product->name }}">
          @endif
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $product->name }}</h5>
            <p class="card-text text-muted mb-2">
              Giá gốc: <strong>{{ number_format($product->base_price,0,',','.') }}₫</strong>
            </p>

            {{-- Các Option riêng --}}
            @foreach($product->optionValues
                     ->groupBy(fn($v) => $v->type->name)
                     as $typeName => $values)
              <div class="mb-2">
                <label class="form-label fw-semibold">{{ $typeName }}:</label>
                <select class="form-select form-select-sm">
                  @foreach($values as $val)
                    <option>
                      {{ $val->value }}
                      @if($val->extra_price)
                        (+{{ number_format($val->extra_price,0,',','.') }}₫)
                      @endif
                    </option>
                  @endforeach
                </select>
              </div>
            @endforeach

            <div class="mt-auto">
              <a href="{{ route('products.show', $product->slug) }}" 
                 class="btn btn-primary btn-sm w-100">
                Xem chi tiết
              </a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <p>Chưa có sản phẩm nào.</p>
      </div>
    @endforelse
  </div>

  {{-- Phân trang --}}
  <div class="d-flex justify-content-center">
    {{ $products->links() }}
  </div>
</div>
@endsection
