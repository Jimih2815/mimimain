{{-- resources/views/profile/edit-mobile.blade.php --}}
@extends('layouts.app-mobile')

@section('title','Hồ sơ của tôi (Mobile)')

@section('content')
<style>
  .mobile-profile {
    padding: 2rem 0;
  }
  .tab-pane {
    padding-top: 1rem;
    padding-bottom: 1rem;
    background-color: #ffffff;
    border-bottom: 1px solid rgba(0, 0, 0, 0.175);
  }
  .card {
    margin: 0 1rem;
  }
  .nav-link {
    color: #4ab3af;
    font-size: 1.2rem;
  }
  .status-pending {
    color: #4ab3af;
    font-weight: bold;
  }
  .status-shipping {
    color: #d1a029;
    font-weight: bold;
  }
  .status-done {
    color: #3f9426;
    font-weight: bold;
  }
  .btn-detail {
    width: 25%;
    font-size: 0.9rem;
    color: #4ab3af;
    font-weight: 600;
    text-decoration: underline;
  }
  .list-group-item {
    padding: 0.75rem 1rem;
  }
  .list-group {
    border-radius: 0;
  }
  .page-item .page-link {
  background-color: #d1a029;
  color: white;
  font-size: 1rem;
  font-weight: 700;
  border-radius: 0px;
  width: 2.3rem;
  height: 2.3rem;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 0px !important;
}

.page-item.active .page-link {
  background-color: white !important;
  color: #d1a029 !important;
  border: 2px solid #d1a029;
}
.modal-title {
  padding-top: 0.5rem;
  color: #4ab3af;
    font-size: 1.5rem;
    font-weight: 800;
}
.fa-xmark {
  font-size: 1.5rem;
    -webkit-text-stroke: 2px #b83232;
    color: white;
}
.modal-content {
  width: 80%;
}
/* ==== Slide-in modal từ phải qua trái ==== */
.modal.modal-slide .modal-dialog{
  transform: translateX(100%);
  transition: transform .3s ease;
  margin: 0;           
  
}
.modal.modal-slide.show .modal-dialog{
  transform: translateX(0);
}
/* Khi đóng – thêm class slide-out để chạy lại về phải */
.modal.modal-slide.slide-out .modal-dialog{
  transform: translateX(100%);
}
.modal-fullscreen .modal-body {
  border-top: 1px solid #4ab3af;
}
.modal-fullscreen .modal-content {
  border-left: 1px solid #4ab3af;
}
.status-help-da-tiep-nhan { color: #4ab3af;font-weight:bold; }

.status-help-dang-xu-ly { color: #fe3b27;font-weight:bold; }

.status-help-hoan-thanh { color: #3f9426;font-weight:bold; }
.btn-read-more {
  color: #4ab3af;
}
</style>

<div class="mobile-profile">
  {{-- Nav tabs --}}
  <ul class="nav nav-tabs" id="mobileTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active"
              id="info-m-tab"
              data-bs-toggle="tab"
              data-bs-target="#info-m"
              type="button"
              role="tab">
        Thông tin
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link"
              id="orders-m-tab"
              data-bs-toggle="tab"
              data-bs-target="#orders-m"
              type="button"
              role="tab">
        Đơn hàng
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link"
              id="help-m-tab"
              data-bs-toggle="tab"
              data-bs-target="#help-m"
              type="button"
              role="tab">
        Trợ giúp
      </button>
    </li>
  </ul>

  <div class="tab-content" id="mobileTabsContent">
    {{-- Thông tin --}}
    <div class="tab-pane fade show active" id="info-m" role="tabpanel">
      @include('profile.partials.info-mobile')
    </div>

    {{-- Đơn hàng --}}
    <div class="tab-pane fade" id="orders-m" role="tabpanel">
      @if($orders->isEmpty())
        <p class="text-center">Chưa có đơn hàng</p>
      @else
        <div id="orders-list-mobile">
          <ul class="list-group">
            @foreach($orders as $o)
              @php
                $statusLabels = [
                  'pending'  => 'Đã tiếp nhận',
                  'shipping' => 'Đang giao hàng',
                  'done'     => 'Đã nhận hàng',
                ];
                $label = $statusLabels[$o->status] ?? ucfirst($o->status);
                $it = $o->items->first();
              @endphp

              {{-- Item summary --}}
              <li class="list-group-item d-flex align-items-start justify-content-between">
                <div class="d-flex align-items-start">
                  <img src="{{ asset('storage/'.$it->product->img) }}"
                       class="me-2"
                       style="width:50px; height:50px; object-fit:cover;">
                  <div>
                    <p class="small mb-1 fw-semibold">
                      {{ $it->product->name }} × {{ $it->quantity }}
                    </p>
                    <p class="small mb-0">
                      Trạng thái:
                      <strong class="status-{{ $o->status }}">{{ $label }}</strong><br>
                      Mã vận đơn:
                      <strong>{{ $o->tracking_number ?: '—' }}</strong>
                    </p>
                  </div>
                </div>
                <button type="button"
                        class="btn-detail border-0 bg-transparent"
                        data-target="#order-detail-{{ $o->id }}">
                  Chi tiết
                </button>
              </li>

              {{-- Template chi tiết ẩn --}}
              <div id="order-detail-{{ $o->id }}" class="d-none">
                <div class="p-3">
                  <div class="mb-3"><strong>Mã đơn:</strong> {{ $o->order_code }}</div>

                  <div class="mb-3"><strong>Sản phẩm:</strong></div>
                  @foreach($o->items as $it)
                    <div class="ps-3 mb-3">
                      {{ $it->product->name }} × {{ $it->quantity }}
                      @if($it->options)
                        <ul class="ps-3 mb-0">
                          @foreach(\App\Models\OptionValue::whereIn('id', $it->options)->with('type')->get() as $v)
                            <li>{{ $v->type->name }}: {{ $v->value }}</li>
                          @endforeach
                        </ul>
                      @endif
                    </div>
                  @endforeach

                  <div class="mb-3"><strong>Thanh toán:</strong> {{ number_format($o->total,0,',','.') }}₫</div>
                  <div class="mb-3"><strong>Hình thức:</strong> {{ $o->payment_method=='cod'?'COD':'Chuyển khoản' }}</div>
                  <div class="mb-3"><strong>Trạng thái:</strong> {{ $label }}</div>
                  <div class="mb-3"><strong>Mã vận đơn:</strong> {{ $o->tracking_number ?: '—' }}</div>
                  <div><strong>Ngày đặt:</strong> {{ $o->created_at->format('d/m/Y H:i') }}</div>
                </div>
              </div>
            @endforeach
          </ul>

          {{-- Pagination --}}
          <div class="mt-3">
            @php
              $current = $orders->currentPage();
              $last    = $orders->lastPage();

              if ($last <= 5) {
                $pages = range(1, $last);
              } else {
                if ($current <= 2) {
                  $pages = [1,2,3, $last-1, $last];
                } elseif ($current >= $last-1) {
                  $pages = [1,2, $last-2, $last-1, $last];
                } else {
                  $pages = [1, $current-1, $current, $current+1, $last];
                }
              }
            @endphp
            @if($last > 1)
              <div class="mt-3">
                <ul class="pagination justify-content-center mb-0 gap-1">
                  @foreach($pages as $p)
                    <li class="page-item {{ $p == $current ? 'active' : '' }}">
                      @if($p == $current)
                        <span class="page-link">{{ $p }}</span>
                      @else
                        <a href="{{ $orders->url($p) }}" class="page-link">{{ $p }}</a>
                      @endif
                    </li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>
        </div>
      @endif
    </div>

    {{-- Trợ giúp --}}
    <div class="tab-pane fade" id="help-m" role="tabpanel">
      @if($orderedRequests->isEmpty())
        <p class="text-center">Chưa có yêu cầu trợ giúp</p>
      @else
        <ul class="list-group">
          @foreach($orderedRequests as $i => $req)
            @php
              $map = [
                'received'   => ['label' => 'Đã tiếp nhận', 'class' => 'received'],
                'processing' => ['label' => 'Đang xử lý',   'class' => 'processing'],
                'done'       => ['label' => 'Hoàn thành',    'class' => 'done'],
              ];
              // $req->status trong DB có thể là 'processing', 'received' ...
              $state = $map[$req->status] ?? ['label' => $req->status, 'class' => \Illuminate\Support\Str::slug($req->status)];
            @endphp

            <li class="list-group-item d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <span class="small d-block">{{ $i+1 }}. {{ Str::limit($req->message, 30) }}</span>
                <span class="small status-help-{{ $state['class'] }}">{{ $state['label'] }}</span>
              </div>

              <button type="button"
                      class="btn-detail border-0 bg-transparent ms-2"
                      data-target="#help-detail-{{ $req->id }}">
                Chi tiết
              </button>
            </li>


            {{-- === template chi tiết ẩn cho từng yêu cầu === --}}
            <div id="help-detail-{{ $req->id }}" class="d-none">
              <div class="p-3">
                {{-- 1) Nội dung --}}
                <div class="mb-3">
                  <strong>Nội dung:</strong>
                  <p class="mt-1 mb-0 truncate">
                    {{ Str::limit($req->message, 120) }}
                    @if(strlen($req->message) > 120)
                      <span class="d-none full-text">{{ $req->message }}</span>
                      <button class="btn-read-more btn btn-link p-0">Xem thêm</button>
                    @endif
                  </p>
                </div>

                {{-- 2) Phản hồi --}}
                <div class="mb-3">
                  <strong>Phản hồi:</strong>
                  <p class="mt-1 mb-0 truncate">
                    {{ $req->response ? Str::limit($req->response, 120) : '—' }}
                    @if($req->response && strlen($req->response) > 120)
                      <span class="d-none full-text">{{ $req->response }}</span>
                      <button class="btn-read-more btn btn-link p-0">Xem thêm</button>
                    @endif
                  </p>
                </div>

                {{-- 3) Trạng thái --}}
                <div>
                  <strong>Trạng thái:</strong>
                  <span class="ms-1 status-help-{{ $state['class'] }}">{{ $state['label'] }}</span>

                </div>
              </div>
            </div>
          @endforeach
        </ul>
      @endif
    </div>

  </div>
</div>

{{-- Modal full-screen --}}
<div class="modal modal-slide" id="detailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-fullscreen d-flex justify-content-end">
    <div class="modal-content">
      <div class="modal-header d-flex justify-content-between">
        <h5 class="modal-title">Chi tiết</h5>
        <button type="button" id="detail-close" class="d-flex bg-transparent align-items-center justify-content-center border-0"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body"></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modalEl  = document.getElementById('detailModal');
  const bsModal  = new bootstrap.Modal(modalEl, { backdrop: false });
  const closeBtn = document.getElementById('detail-close');

  /* ====== TOOL: mở modal ====== */
  function openModal(html){
    modalEl.classList.remove('slide-out');
    modalEl.querySelector('.modal-body').innerHTML = html;
    bsModal.show();
  }

  /* ====== TOOL: đóng modal (slide) ====== */
  function closeModal(){
    modalEl.classList.add('slide-out');
    const dlg = modalEl.querySelector('.modal-dialog');
    const end = (e)=>{
      if(e.propertyName!=='transform') return;
      dlg.removeEventListener('transitionend',end);
      bsModal.hide();
    };
    dlg.addEventListener('transitionend',end);
  }

  /* ESC / click ngoài => slide-out */
  modalEl.addEventListener('hide.bs.modal', e=>{
    if(!modalEl.classList.contains('slide-out')){
      e.preventDefault();
      closeModal();
    }
  });
  modalEl.addEventListener('hidden.bs.modal', ()=>modalEl.classList.remove('slide-out'));

  /* ====== Delegation click toàn trang ====== */
  document.addEventListener('click', e=>{
    /* 1) Nút Chi tiết (đơn hàng & trợ giúp) */
    const btn = e.target.closest('.btn-detail');
    if(btn){
      const tpl = document.querySelector(btn.dataset.target);
      if(tpl) openModal(tpl.innerHTML);
      return;
    }

    /* 2) Phân trang AJAX đơn hàng */
    const link = e.target.closest('#orders-list-mobile a.page-link');
    if(link){
      e.preventDefault();
      fetch(link.href,{headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(r=>r.text())
        .then(html=>{
          const doc=new DOMParser().parseFromString(html,'text/html');
          const list=doc.querySelector('#orders-list-mobile');
          if(list) document.querySelector('#orders-list-mobile').innerHTML=list.innerHTML;
        });
    }

    /* 3) “Xem thêm” trong modal */
    const more = e.target.closest('.btn-read-more');
    if(more){
      const p = more.parentElement;
      const full = p.querySelector('.full-text');
      if(!full) return;
      p.firstChild.textContent = '';          // xoá đoạn rút gọn
      p.insertBefore(document.createTextNode(full.textContent), more);
      full.remove();
      more.remove();                          // bỏ nút
    }
  });

  /* ====== Auto chọn tab Đơn hàng nếu #orders-m ====== */
  if(location.hash==='#orders-m'){
    document.getElementById('orders-m-tab')?.click();
  }

  /* ====== Nút đóng X ====== */
  closeBtn.addEventListener('click', closeModal);
});
</script>
@endpush





