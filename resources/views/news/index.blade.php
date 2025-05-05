{{-- resources/views/news/index.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
    /* Slider button styles */
    .news-next,
    .news-prev {
        position: static !important;
        top: auto !important;
        margin: 0 !important;
        transform: none !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 3rem !important;
        height: 3rem !important;
        background-color: #4ab3af !important;
        border: 1px solid #4ab3af !important;
        border-radius: 50% !important;
        color: white !important;
        font-size: 1.5rem;
    }

    /* Ẩn summary mặc định của Laravel */
    nav[aria-label="Pagination Navigation"] .flex-1 {
        display: none !important;
    }

    /* Tùy chỉnh Bootstrap‐5 pagination */
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
        padding-left: 0;
        list-style: none;
    }
    .pagination-lg .page-link {
        padding: 0.5rem 0.75rem;
        border-radius: 50% !important;
        min-width: 2.5rem;
        text-align: center;
    }
    .page-item.disabled .page-link {
        background-color: #ccc;
        color: #666;
    }
    .page-item.active .page-link {
        background-color:rgb(115, 204, 194);
        color: #fff;
    }
    .page-link {
        background-color: #4ab3af;
        color: #fff;
    }
    .page-link:hover {
        background-color: #66c2c0;
        color: #fff;
    }
    .pagination-summary, 
    .text-muted {
    display: none !important;
    }
    .page-item.active .page-link {
    border-color: #4ab3af !important;
    color: #fff !important;
    }
</style>

<div class="w-50 mx-auto mt-5 mb-5">
    <h1 class="mb-5" style="color:#b83232;">Tin tức mới nhất</h1>

    @foreach($posts as $post)
        <div class="d-flex mb-4 border-bottom pb-3">
            {{-- Ảnh thumbnail --}}
            @if($post->thumbnail)
                <img src="{{ asset('storage/'.$post->thumbnail) }}"
                     class="me-3"
                     style="width:200px; height:200px; object-fit:cover;">
            @else
                <div class="bg-secondary me-3"
                     style="width:200px; height:200px;"></div>
            @endif

            <div class="flex-fill d-flex flex-column justify-content-start" style="height:200px">
                <h5 style="height: 10%;">
                    <a href="{{ route('news.show', $post->slug) }}"
                       class="text-decoration-none"
                       style="color:#4ab3af; font-size:1.5rem;">
                        {{ $post->title }}
                    </a>
                </h5>
                <small class="text-muted" style="height: 10%;">
                    {{ $post->created_at->format('d/m/Y') }}
                </small>

                <div class="post-excerpt mt-2" style="width:100%; height: 80%; overflow:hidden;">
                    {!! \Illuminate\Support\Str::limit(
                          strip_tags($post->content),
                          750,
                          '...'
                        ) !!}
                </div>
            </div>
        </div>
    @endforeach

    {{-- Pagination Bootstrap-5 --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $posts->links('pagination::bootstrap-5', ['paginator' => $posts, 'link_limit' => 5]) }}
    </div>
</div>

{{-- ===== Slider “Top Trending” ===== --}}
@if(isset($selectedCollection) && $selectedCollection->products->isNotEmpty())
    <div class="w-100 news-slider-wrapper mb-5 mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Top Trending Hiện Nay</h3>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary news-prev">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button class="btn btn-outline-secondary news-next">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <div class="swiper news-swiper">
            <div class="swiper-wrapper">
                @foreach($selectedCollection->products as $product)
                    <div class="swiper-slide">
                        <a class="text-decoration-none" href="{{ route('products.show', $product->slug) }}">
                            <div class="ratio ratio-1x1 overflow-hidden">
                                <img src="{{ asset('storage/'.($product->sub_img[0] ?? $product->img)) }}"
                                     class="w-100 h-100 object-fit-cover object-position-center"
                                     alt="{{ $product->name }}">
                            </div>
                            <p class="mt-3 mb-1 text-center fw-semibold"
                               style="color:#333333; font-size:1.1rem;">
                                {{ $product->name }}
                            </p>
                            <p class="text-center" style="color:#333333;">
                                {{ number_format($product->base_price,0,',','.') }}₫
                            </p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
@endsection
