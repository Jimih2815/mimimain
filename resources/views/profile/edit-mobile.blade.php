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
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span class="small">{{ $i+1 }}. {{ Str::limit($req->message, 30) }}</span>
              <button type="button"
                      class="btn-detail border-0 bg-transparent"
                      data-target="#help-full-table">
                Chi tiết
              </button>
            </li>
          @endforeach
        </ul>
      @endif

      <div id="help-full-table" class="d-none">
        <table class="table table-bordered text-center mb-0">
          <thead>
            <tr><th>#</th><th>Nội dung</th><th>Phản hồi</th><th>Trạng thái</th></tr>
          </thead>
          <tbody>
            @foreach($orderedRequests as $i => $req)
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $req->message }}</td>
                <td>{{ $req->response?:'—' }}</td>
                <td>{{ $req->status }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
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
  // 👉 tạo modal KHÔNG backdrop
  const bsModal  = new bootstrap.Modal(modalEl, { backdrop: false });
  const closeBtn = document.getElementById('detail-close');

  /* === Mở === */
  function openModal(html){
    modalEl.classList.remove('slide-out');
    modalEl.querySelector('.modal-body').innerHTML = html;
    bsModal.show();
  }

  /* === Đóng: slide-out xong mới hide === */
  function closeModal(){
    modalEl.classList.add('slide-out');

    const dlg = modalEl.querySelector('.modal-dialog');
    const onEnd = (e) => {
      if (e.propertyName !== 'transform') return;
      dlg.removeEventListener('transitionend', onEnd);
      bsModal.hide();          // không còn backdrop để dọn
    };
    dlg.addEventListener('transitionend', onEnd);
  }

  /* Chặn Bootstrap ẩn tức thì khi ESC / click ngoài */
  modalEl.addEventListener('hide.bs.modal', e => {
    if (!modalEl.classList.contains('slide-out')) {
      e.preventDefault();
      closeModal();
    }
  });

  modalEl.addEventListener('hidden.bs.modal', () => {
    modalEl.classList.remove('slide-out');
  });

  /* Auto tab */
  if (location.hash === '#orders-m') {
    document.getElementById('orders-m-tab')?.click();
  }

  /* Delegation */
  document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-detail');
    if (btn){
      const tpl = document.querySelector(btn.dataset.target);
      tpl && openModal(tpl.innerHTML);
      return;
    }

    const link = e.target.closest('#orders-list-mobile a.page-link');
    if (link){
      e.preventDefault();
      fetch(link.href, {headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(r => r.text())
        .then(html => {
          const doc  = new DOMParser().parseFromString(html,'text/html');
          const list = doc.querySelector('#orders-list-mobile');
          if (list) document.querySelector('#orders-list-mobile').innerHTML = list.innerHTML;
        });
    }
  });

  closeBtn.addEventListener('click', closeModal);
});
</script>
@endpush




