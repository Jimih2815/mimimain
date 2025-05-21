@extends('layouts.app-mobile')
@section('title','Yêu thích (Mobile)')

@section('content')
<style>
    .trang-yeu-thich-mobile {

    }
  .cart-wrapper { padding-bottom: calc(4rem + 3rem); }
  .row-products {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 10px;
}
.product-card {
    width: 48.5%;
    background: #fff;
    border-radius: 0.5rem;
    overflow: hidden;
    margin-bottom: 1.5rem;
}
  .product-card img {
    width: 100%;
    display: block;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    overflow: hidden;
}
  .product-body { 
      display: flex;
  flex-direction: column;
  justify-content: space-between;
    padding: 0.5rem; 
    height: 7.5rem;
}
  .product-body .price {
    color: #fe3b27;
    font-weight: 600;
    font-size: 1.2rem;
}
  .btn-detail { width:100%; margin-top:0.5rem; }
  /* panel chung */
  .detail-panel {
    position: fixed; bottom:0; left:0;
    width:100%; height:80vh;
    background:#fff;
    transform: translateY(100%);
    transition: transform .3s ease;
    z-index: 1002;
    overflow-y: auto;
  }
  .detail-panel.open { transform: translateY(0); }
  .detail-overlay {
    display:none; position:fixed;
    top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,0.3); z-index:1001;
  }
  .detail-overlay.open { display:block; }
  .panel-close { position:absolute; top:1rem; right:1rem; font-size:1.5rem; border:none; background:transparent; }
  .panel-content { padding:2rem 1rem 1rem; }
  .cart-button { position: sticky; bottom:3rem; width:100%; padding:1rem; background:#4ab3af; color:#fff; border:none; font-weight:700; }
   .nut-xem {
    color: #4ab3af;
    font-size: 1rem;
    font-weight: 700;
    padding: 0;
    height: 2rem;
  }
  .fa-arrow-right {
    font-size: 1rem;
  }
    .original-price {
    /* tuỳ chỉnh font-size nếu cần */
    /* ví dụ nhỏ hơn một chút cho đỡ chiếm chỗ */
    font-size: 0.85rem;
    text-decoration: line-through;
    }

</style>
<div class="trang-yeu-thich-mobile ms-1 me-1 pt-3 pb-3">
    <h1 class="pb-3 ps-1">Sản phẩm yêu thích</h1>
    <div class="cart-wrapper">
    <div class="row-products">
        @foreach($products as $p)
            <div class="product-card" data-id="{{ $p->id }}">
                @if($p->img)
                <a href="{{ route('products.show', $p->slug) }}" class="d-block">
                    <img src="{{ asset('storage/'.$p->img) }}" alt="{{ $p->name }}">
                </a>
                @endif
                <div class="product-body">
                <h6 class="xuong-2-dong m-0">{{ $p->name }}</h6>
                <div class="d-flex align-items-center mt-2">
                    <!-- Giá sale (giá gốc bạn muốn hiển thị) -->
                    <div class="price">
                    {{ number_format($p->base_price, 0, ',', '.') }}₫
                    </div>
                    <!-- Giá “đắt xắt ra miếng” cộng thêm 40%, gạch ngang -->
                    <div class="original-price ms-2 text-muted" style="text-decoration: line-through;">
                    {{ number_format($p->base_price * 1.4, 0, ',', '.') }}₫
                    </div>
                </div>
                <button class="nut-xem border-0 bg-transparent btn-detail d-flex justify-content-center align-items-center mt-auto" data-id="{{ $p->id }}">
                    <p class="m-0 p-0 d-flex justify-content-center align-items-center">Chi Tiết</p>
                    <i class="ms-2 fa-solid fa-arrow-right d-flex justify-content-center align-items-center"></i>
                </button>
                </div>
            </div>
        @endforeach

    </div>
    </div>

    <div id="detail-overlay" class="detail-overlay"></div>

    @foreach($products as $p)
    {{-- Panel riêng cho mỗi sản phẩm --}}
    <div id="detail-{{ $p->id }}" class="detail-panel">
        <button class="panel-close" data-id="{{ $p->id }}">&times;</button>
        <div class="panel-content">
        <h4>{{ $p->name }}</h4>
        <form id="detail-form-{{ $p->id }}"
                action="{{ route('cart.add',$p->id) }}"
                method="POST">
            @csrf

            {{-- Options --}}
            @foreach($p->optionValues->groupBy(fn($v)=>$v->type->name) as $typeName => $vals)
            @php $typeId = $vals->first()->type->id; @endphp
            <div class="mb-2">
                <label class="form-label">{{ $typeName }}</label>
                <div class="d-flex flex-wrap option-group"
                    data-first="{{ $loop->first?1:0 }}">
                @foreach($vals as $val)
                    <div class="option-item"
                        data-type="{{ $typeId }}"
                        data-val="{{ $val->id }}"
                        data-extra="{{ $val->extra_price }}"
                        style="padding:.3rem .6rem; border:1px solid #ccc; border-radius:.25rem; margin:.2rem; cursor:pointer;">
                    {{ $val->value }}
                    </div>
                @endforeach
                </div>
                <input type="hidden"
                    name="options[{{ $typeId }}]"
                    id="opt-{{ $p->id }}-{{ $typeId }}"
                    required>
            </div>
            @endforeach

            {{-- Nút thêm --}}
            <button type="submit"
                    class="btn btn-primary w-100 mt-3">
            Thêm vào giỏ
            </button>
        </form>
        </div>
    </div>
    @endforeach

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{
  // 1) Mở/đóng panel
  const overlay = document.getElementById('detail-overlay');
  document.querySelectorAll('.btn-detail').forEach(b=>{
    b.addEventListener('click',()=>{
      const id=b.dataset.id;
      document.getElementById('detail-'+id).classList.add('open');
      overlay.classList.add('open');
    });
  });
  document.querySelectorAll('.panel-close').forEach(x=>{
    x.addEventListener('click',()=>{
      const id=x.dataset.id;
      document.getElementById('detail-'+id).classList.remove('open');
      overlay.classList.remove('open');
    });
  });
  overlay.addEventListener('click',()=>{
    document.querySelectorAll('.detail-panel.open').forEach(d=>{
      d.classList.remove('open');
    });
    overlay.classList.remove('open');
  });

  // 2) AJAX thêm favorite (nếu cần) – copy từ desktop

  // 3) Logic chọn option & submit cart
  @foreach($products as $p)
  ;(function(){
    const pid = '{{ $p->id }}';
    const panel = document.getElementById('detail-'+pid);
    const form  = document.getElementById('detail-form-'+pid);
    const opts  = panel.querySelectorAll('.option-item');
    const selected = {};
    function updateHidden(type,val){
      selected[type]=val;
      panel.querySelector(`#opt-${pid}-${type}`).value=val;
    }
    opts.forEach(el=>{
      el.addEventListener('click',()=>{
        const t=el.dataset.type, v=el.dataset.val;
        // bỏ chọn nhóm
        panel.querySelectorAll(`.option-item[data-type="${t}"]`)
             .forEach(s=>s.classList.remove('bg-primary','text-white'));
        el.classList.add('bg-primary','text-white');
        updateHidden(t,v);
      });
    });
    form.addEventListener('submit',e=>{
      // kiểm option đầy đủ
      const miss = Array.from(panel.querySelectorAll('input[type="hidden"]'))
                        .some(i=>!i.value);
      if(miss){
        e.preventDefault();
        alert('Vui lòng chọn đủ tuỳ chọn.');
      }
    });
  })();
  @endforeach
});
</script>
@endpush
