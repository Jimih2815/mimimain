{{-- resources/views/home-mobile.blade.php --}}
@extends('layouts.app-mobile')

@section('title','Trang Chủ Mobile')

@section('content')
  @if($home->popup_image)
    <div id="homePopup" class="home-popup-overlay" aria-hidden="true">
      <div class="home-popup-box" role="dialog" aria-modal="true">
        <button type="button" class="home-popup-close" aria-label="Dong">
          <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
        <img src="{{ asset('storage/'.$home->popup_image) }}" alt="Popup">
      </div>
    </div>
  @endif
  {{-- 0) Pre-banner --}}
  @if($home->pre_banner_title)
    <div class="pre-banner text-center py-2 bg-light w-100">
      <h5 class="mb-0" style="font-size:1.1rem;">{{ $home->pre_banner_title }}</h5>
      @if($home->preBannerCollection)
        <a href="{{ route('collections.show',$home->preBannerCollection->slug) }}"
           class="nut-dau-trang mx-auto">
           {{ $home->pre_banner_button_text }}  <!-- <i class="fa-solid fa-angles-right"></i> -->
        </a>
      @endif
    </div>
  @endif

  {{-- 1) Banner slider (Mobile ưu tiên banner mobile nếu có) --}}
  @php
    $mobileBanners = isset($bannerImagesMobile) ? $bannerImagesMobile : collect();
    $desktopBanners = isset($bannerImagesDesktop) ? $bannerImagesDesktop : collect();
    $bannersToUse = $mobileBanners->count() ? $mobileBanners : $desktopBanners;
  @endphp

  @if($bannersToUse->count() > 0)
    <div class="full-banner position-relative mb-">
      <div class="swiper home-banner-swiper w-100 h-100">
        <div class="swiper-wrapper w-100 h-100">
          @foreach($bannersToUse as $b)
            <div class="swiper-slide w-100 h-100">
              @php
                $href = $b->collection
                  ? route('collections.show', $b->collection->slug)
                  : '#';
              @endphp
              <a href="{{ $href }}" class="d-block w-100 h-100">
                <img src="{{ asset('storage/'.$b->image) }}"
                    alt="Home Banner {{ $loop->iteration }}"
                    class="w-100 mobile-banner-img">
              </a>
            </div>
          @endforeach
        </div>
        <div class="swiper-pagination"></div>
      </div>

      @if($home->show_button && $home->buttonCollection)
        <a href="{{ route('collections.show', $home->buttonCollection->slug) }}"
          class="btn btn-lg btn-primary nut-banner position-absolute top-50 start-50 translate-middle">
          {{ $home->button_text }}
        </a>
      @endif
    </div>
  @else
    @php
      $bannerPath = $home->banner_image_mobile ?: $home->banner_image;
    @endphp
    @if($bannerPath)
      <div class="full-banner position-relative mb-">
        <img src="{{ asset('storage/'.$bannerPath) }}"
            alt="Home Banner"
            class="w-100 mobile-banner-img">
        @if($home->show_button && $home->buttonCollection)
          <a href="{{ route('collections.show', $home->buttonCollection->slug) }}"
            class="btn btn-lg btn-primary nut-banner position-absolute top-50 start-50 translate-middle">
            {{ $home->button_text }}
          </a>
        @endif
      </div>
    @endif
  @endif

<div class="slider-cont">
    <!-- {{-- 2) Intro text --}}
    @if($home->intro_text)
      <div class="text-center mb-4 ms-3 me-3">
        <p class="lead">{{ $home->intro_text }}</p>
        @if($home->introButtonCollection)
          <a href="{{ route('collections.show', $home->introButtonCollection->slug) }}"
             class="btn-mimi nut-vang mx-auto">
            {{ $home->intro_button_text }}
          </a>
        @endif
      </div>
    @endif -->
{{-- 3) Collection (xếp dọc, không slider) --}}
<div class="slider-full-width ms-0 me-0 px-0" style="padding-top: 4rem;">
  <div class="d-flex justify-content-between px-3 align-items-center">
    <h3 class="mb-0 tieu-de">{{ $home->collection_slider_title ?: 'Khám phá bộ sưu tập' }}</h3>
  </div>

  <div class="collection-stack">
    @foreach($sliders as $s)
      <div class="swiper-slide swiper-slide-active collection-stack-item">
        <a href="{{ route('collections.show', $s->collection->slug) }}" class="d-block">
          <img src="{{ asset('storage/'.$s->image) }}"
               alt="{{ $s->text }}"
               class="w-100 collection-stack-img">
          <p class="the-p collection-stack-caption">{{ $s->text }}</p>
          <span class="collection-stack-shop-btn">Shop</span>
        </a>
      </div>
    @endforeach
  </div>
</div>

{{-- 4A) Khám phá bộ sưu tập --}}
    @if($home->collection_section_title)
      <div class="text-center mb-3 ms-3 me-3 " style="padding-top: 4rem;">
        <h2 class="" style="font-size: 1.5rem;">{{ $home->collection_section_title }}</h2>
        @if($home->collectionSectionCollection)
          <a href="{{ route('collections.show', $home->collectionSectionCollection->slug) }}"
             class="btn-mimi nut-do mx-auto" style="padding: 0.25rem 1rem;">
            {{ $home->collection_section_button_text }}
          </a>
        @endif
      </div>
    @endif

  {{-- 4B) Section Images slider tự động --}}
  <div class="swiper section-images-swiper  ms-1 me-1">
    <div class="swiper-wrapper">
      @foreach($sectionImages as $img)
        <div class="swiper-slide section-img">
          <a class ="img-cont" href="{{ $img->collection
                        ? route('collections.show', $img->collection->slug)
                        : '#' }}">
            <img src="{{ asset('storage/'.$img->image) }}"
                alt="Section Image {{ $loop->iteration }}"
                class="w-100 rounded">
          </a>
        </div>
      @endforeach
    </div>
  </div>

    

    {{-- 5) Product Slider --}}
    <div class="slider-home-product mb-5 ms-1 me-1">
      <div class="d-flex justify-content-between align-items-center" style="padding-left: 1rem; padding-bottom: 1rem; padding-top: 4rem; ">
        <h3 class="mb-0" style="font-size:1.5rem;">{{ $home->product_slider_title ?: 'Sản phẩm nổi bật' }}</h3>
        <div class="nut-navi">
          <button class="btn btn-outline-secondary me-2 home-product-prev">
             <i class="bi bi-chevron-left fs-4"></i>
          </button>
          <button class="btn btn-outline-secondary home-product-next">
            <i class="bi bi-chevron-right fs-4"></i>
          </button>
        </div>
      </div>
      <div class="swiper home-product-swiper">
        <div class="swiper-wrapper">
          @foreach($productSliders as $ps)
            <div class="swiper-slide">
              <a href="{{ route('products.show', $ps->product->slug) }}">
                <img src="{{ asset('storage/'.$ps->image) }}"
                     alt="{{ $ps->product->name }}"
                     class="w-100 rounded mb-2">
                <p class="text-start mb-0 the-p-2">{{ $ps->product->name }}</p>
                <p class="text-start text-danger fw-bold" style="font-size: 1.2rem;">
                  {{ number_format($ps->product->base_price,0,',','.') }}₫
                </p>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const popup = document.getElementById('homePopup');
    if (!popup) return;

    const closeBtn = popup.querySelector('.home-popup-close');
    const closePopup = () => {
      popup.classList.remove('is-open');
      document.body.classList.remove('home-popup-open');
    };

    popup.classList.add('is-open');
    document.body.classList.add('home-popup-open');

    if (closeBtn) {
      closeBtn.addEventListener('click', closePopup);
    }

    popup.addEventListener('click', (e) => {
      if (e.target === popup) closePopup();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closePopup();
    });
  });
</script>
@endpush

@push('styles')
<style>
  /* === Banner full màn hình dọc (Mobile) === */
  .full-banner {
    width: 100%;
    height: auto; 
    aspect-ratio: 9 / 12;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .mobile-banner-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
  }

  /* Ẩn navi (giữ cho đồng bộ layout cũ) */
  .nut-navi { display: none !important; }

  /* === Collection stack (thay slider) === */
.collection-stack{
  display:flex;
  flex-direction:column;
  /* gap:22px; */
  padding:0;              
  margin:0;
}
.collection-stack-item a{
  position:relative;
  display:block;
}
/* lớp tối gradient để chữ trắng luôn đọc được dù ảnh nhiều màu / trắng */
.collection-stack-item a::after{
  content:"";
  position:absolute;
  left:0; right:0; bottom:0;
  height:45%;
  background:linear-gradient(to top, rgba(0,0,0,.65), rgba(0,0,0,0));
  pointer-events:none;
  z-index:1;
}
.collection-stack-img{
  width:100%;
  height:70svh;       
  max-height:70vh;
  object-fit:cover;
  border-radius:0;    
  display:block;
}
.collection-stack-caption{
  position:absolute;
  left:0; right:0;
  bottom:56px; /* chừa chỗ cho nút Shop */
  margin:0;
  padding:0 14px;
  text-align:center;
  color:#fff;
  font-weight:800;
  z-index:2; /* nằm trên gradient */
  text-shadow:
    0 2px 10px rgba(0,0,0,.85),
    0 0 2px rgba(0,0,0,.95);
}

/* Nút Shop giống mẫu (pill trắng, đặt dưới caption) */
.collection-stack-shop-btn{
  position:absolute;
  left:13%;
  bottom:40px;
  transform:translateX(-50%);
  z-index:2;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  padding:7px 18px;
  border-radius:999px;
  background:#fff;
  color:#111;
  font-weight:800;
  font-size:14px;
  line-height:1;
  border:1px solid rgba(255,255,255,.75);
  box-shadow:0 6px 18px rgba(0,0,0,.25);
  text-decoration:none;
}

/* chạm vào nhìn "đã" hơn */
.collection-stack-item a:hover .collection-stack-shop-btn,
.collection-stack-item a:focus .collection-stack-shop-btn{
  transform:translateX(-50%) translateY(-1px);
  box-shadow:0 10px 22px rgba(0,0,0,.32);
}

/* Section Images (Swiper) */

  .section-images {
    display: flex;
    flex-direction: row;
    gap: 12px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
  }
  .section-images .section-img {
    flex: 0 0 85%;
    scroll-snap-align: start;
    position: relative;
  }
  .section-images::-webkit-scrollbar { display: none; }
  .section-images .section-img img {
    width: 100% !important;
    height: 100% !important;
    border-radius: 10px;
    object-fit: cover;
  }

  .lead {
    font-size: 1.1rem;
    font-weight: 400;
  }
  .nut-dau-trang { 
    font-size: 0.9rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
    color: #4ab3af;
    font-weight: 600;
    text-decoration: underline;
  }
  .nut-dau-trang .fa-angles-right {
    font-size: 0.8rem;
    padding-top: 1.5px;
  }
  .the-p {
    text-align: justify;
    color: white;
    font-size: 1.8rem;
    font-weight: 500;
    padding-bottom: 1.5rem;
  }
  .the-p-2 {
    color: black;
    font-size: 1rem;
    font-weight: 500;
  }
  .img-cont {
    width: 100%;
    display: flex;
    aspect-ratio: 5 / 3;
    height: 100%;
    object-fit: cover;
    overflow: hidden;
    justify-content: center;
    align-items: center;
  }
  .tieu-de {
    font-size: 1.5rem;
    padding-bottom: 1rem;
  }

  .home-popup-overlay {
    position: fixed;
    inset: 0;
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.6);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s ease, visibility 0.2s ease;
  }

  .home-popup-overlay.is-open {
    opacity: 1;
    visibility: visible;
  }

  .home-popup-box {
    position: relative;
    max-width: 92vw;
    max-height: 90vh;
  }

  .home-popup-box img {
    display: block;
    max-width: 92vw;
    max-height: 85vh;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
  }

  .home-popup-close {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    border: none;
    background: transparent;
    color: #fff;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: none;
  }

  .home-popup-close:hover {
    background: transparent;
  }

  body.home-popup-open {
    overflow: hidden;
  }
</style>
@endpush
