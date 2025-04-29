@extends('layouts.app')

@section('title', 'Sản Phẩm Yêu Thích')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Sản Phẩm Yêu Thích</h1>

    @if ($products->isEmpty())
        <div class="alert alert-info text-center">
            Bạn chưa đánh dấu sản phẩm nào.
        </div>
    @else
        <div class="row g-4">
            @foreach ($products as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <a href="{{ route('products.show', $product->slug) }}">
                            <img src="{{ asset('storage/'.$product->img) }}"
                                 class="card-img-top"
                                 alt="{{ $product->name }}">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-2">
                                <a href="{{ route('products.show', $product->slug) }}"
                                   class="text-decoration-none text-dark">
                                    {{ Str::limit($product->name, 30) }}
                                </a>
                            </h5>
                            <p class="card-text mt-auto fw-bold">
                                {{ number_format($product->base_price, 0, ',', '.') }}₫
                            </p>
                            <button type="button"
                                    class="btn btn-outline-danger btn-toggle-fav mt-3"
                                    data-id="{{ $product->id }}">
                                Xóa khỏi yêu thích
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Khi DOM sẵn sàng
document.addEventListener('DOMContentLoaded', function(){
    var buttons = document.querySelectorAll('.btn-toggle-fav');
    buttons.forEach(function(btn){
        btn.addEventListener('click', function(){
            var productId = this.getAttribute('data-id');
            var token     = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            // Gọi AJAX toggle favorite
            fetch('/favorites/toggle/' + productId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
            })
            .then(function(res){ return res.json(); })
            .then(function(json){
                if (json.added) {
                    // Nếu vừa add thì đổi nút
                    btn.textContent = 'Đã thêm yêu thích';
                    btn.classList.remove('btn-outline-danger');
                    btn.classList.add('btn-success');
                } else {
                    // Nếu remove thì ẩn thẻ card luôn
                    var cardCol = btn.closest('.col-6, .col-md-4, .col-lg-3');
                    if (cardCol) cardCol.remove();
                }
            })
            .catch(function(){
                alert('Có lỗi, vui lòng thử lại.');
            });
        });
    });
});
</script>
@endpush
