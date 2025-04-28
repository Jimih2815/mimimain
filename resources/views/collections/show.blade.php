{{-- resources/views/collections/show.blade.php --}}
@extends('layouts.app')

{{-- ▼ BẮT ĐẦU CHÈN SIDEBAR --}}
@section('sidebar')
  @include('components.sidebar')
@endsection
{{-- ▲ KẾT THÚC CHÈN SIDEBAR --}}

@section('content')
<div class="py-4 tat-ca-san-pham-cont">
  <h1 class="mb-4">{{ $collection->name }}</h1>

  @if($collection->description)
    <p class="text-muted mb-4">{{ $collection->description }}</p>
  @endif

  <div class="row">
    @forelse($collection->products as $product)
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          @if($product->img)
            <a href="{{ route('products.show', $product->slug) }}">
              <img 
                src="{{ asset('storage/'.$product->img) }}" 
                class="card-img-top anh-chinh-san-pham" 
                alt="{{ $product->name }}">
            </a>
          @else
            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                 style="height:200px">
              No Image
            </div>
          @endif

          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $product->name }}</h5>
            <p class="card-text text-muted mb-2">
              Giá: <strong>{{ number_format($product->base_price,0,',','.') }}₫</strong>
            </p>

            @foreach(
              $product->optionValues
                      ->groupBy(fn($v) => $v->type->name)
                      as $typeName => $values
            )
              <div class="option-list mb-3">
                <span class="option-type fw-semibold me-2">{{ $typeName }}:</span>
                <div class="d-flex flex-row flex-nowrap overflow-auto option-items">
                  @foreach($values as $val)
                    <span class="option-item me-3">{{ $val->value }}</span>
                  @endforeach
                </div>
              </div>
            @endforeach

            <!-- <a href="{{ route('products.show', $product->slug) }}"
               class="btn btn-primary mt-auto nut-xem-chi-tiet-san-pham">
              Xem chi tiết
            </a> -->
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <p>Chưa có sản phẩm nào trong bộ sưu tập này.</p>
      </div>
    @endforelse
  </div>

  {{-- Phân trang nếu có paginate --}}
  @if(method_exists($collection->products, 'links'))
    <div class="mt-4 nut-dieu-huong">
      {{ $collection->products->links() }}
    </div>
  @endif
</div>
@endsection
