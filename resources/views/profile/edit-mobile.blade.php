{{-- resources/views/profile/edit-mobile.blade.php --}}
@extends('layouts.app-mobile')

@section('title','Hồ sơ của tôi (Mobile)')

@section('content')
<div class="mobile-profile px-3 py-4">
  {{-- Nav tabs --}}
  <ul class="nav nav-tabs mb-3" id="mobileTabs" role="tablist">
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
        <ul class="list-group">
          @foreach($orders as $o)
            @php $it = $o->items->first(); @endphp
            <li class="list-group-item d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <img src="{{ asset('storage/'.$it->product->img) }}"
                     class="me-2"
                     style="width:50px; height:50px; object-fit:cover;">
                <span class="small">{{ $it->product->name }} × {{ $it->quantity }}</span>
              </div>
              <button type="button"
                      class="btn btn-link btn-detail"
                      data-target="#orders-full-table">
                Xem chi tiết
              </button>
            </li>
          @endforeach
        </ul>
      @endif

      {{-- Bảng ẩn --}}
      <div id="orders-full-table" class="d-none">
        <table class="table table-bordered text-center mb-0">
          <thead><tr>
            <th>#</th><th>Mã đơn</th><th>Sản phẩm</th><th>Thanh toán</th>
            <th>Hình thức</th><th>Mã vận đơn</th><th>Trạng thái</th><th>Ngày đặt</th>
          </tr></thead>
          <tbody>
            @foreach($orders as $i => $o)
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $o->order_code }}</td>
                <td>
                  @foreach($o->items as $it)
                    {{ $it->product->name }} × {{ $it->quantity }}<br>
                  @endforeach
                </td>
                <td>{{ number_format($o->total,0,',','.') }}₫</td>
                <td>{{ $o->payment_method=='cod'?'COD':'Chuyển khoản' }}</td>
                <td>{{ $o->tracking_number?:'—' }}</td>
                <td>{{ $o->status }}</td>
                <td>{{ $o->created_at->format('d/m H:i') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
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
                      class="btn btn-link btn-detail"
                      data-target="#help-full-table">
                Xem chi tiết
              </button>
            </li>
          @endforeach
        </ul>
      @endif

      <div id="help-full-table" class="d-none">
        <table class="table table-bordered text-center mb-0">
          <thead><tr><th>#</th><th>Nội dung</th><th>Phản hồi</th><th>Trạng thái</th></tr></thead>
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

  {{-- Modal --}}
  <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Chi tiết</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.mobile-profile { max-width:480px; margin:auto; }
.btn-detail { font-size:0.9rem; }
.list-group-item { padding:.75rem 1rem; }
.modal-body table { width:100%; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = document.querySelector(btn.getAttribute('data-target'));
      if (!target) return;
      document.querySelector('#detailModal .modal-body').innerHTML = target.innerHTML;
      new bootstrap.Modal(document.getElementById('detailModal')).show();
    });
  });
});
</script>
@endpush
