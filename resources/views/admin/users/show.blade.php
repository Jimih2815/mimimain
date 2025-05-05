{{-- resources/views/admin/user/show.blade.php --}}
@extends('layouts.admin')

{{-- === Style overrides only for this page === --}}
@push('styles')
<style>
  /* 1) Table responsive */
  .trang-admin-user .table-tro-giup {
    table-layout: auto !important;
    width: 100%;
  }

  .trang-admin-user .table-tro-giup td {
    white-space: normal;
    word-break: break-word;
    vertical-align: top;
    padding: 0.25rem !important;
  }

  /* 2) Auto-resize script base style */
  .auto-resize {
    width: 100%;
    box-sizing: border-box;
    resize: none;
    overflow: hidden;
    font-size: .875rem;
    line-height: 1.4;
    border: none;
  }

  /* 3) Make textarea in Phản hồi column fill full cell height */
  .trang-admin-user .table-tro-giup td.form-cell {
    position: relative;
    padding: 0.25rem !important;
  }
  .trang-admin-user .table-tro-giup td.form-cell .auto-resize {
    position: absolute !important;
    top: 0; left: 0; right: 0; bottom: 0;
    margin: 0 !important;
    width: 100% !important;
    height: 100% !important;
    overflow-y: auto !important;
    resize: none !important;
    box-sizing: border-box;
  }
  .table-tro-giup thead th {
  background-color: #4ab3af;
  color: white;
  }
  .nav-link {
      color: #4ab3af !important;
      font-size: 1.2rem;
    }
  .nav-link.active {
    color: #333333 !important;

  }  
</style>
@endpush

@section('content')
<div class="container-fluid trang-admin-user">
  <h1 class="mb-4">User #{{ $user->id }}: {{ $user->name }}</h1>

  <p><strong>Email:</strong> {{ $user->email }}</p>
  <p><strong>Đăng ký:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>

  <form action="{{ route('admin.users.resetPassword', $user) }}"
        method="POST" class="d-inline-block me-2">
    @csrf
    <button style="padding: 0.5rem 2rem;" class="bt-mimi nut-vang">Reset mật khẩu về 123456789</button>
  </form>
  <form action="{{ route('admin.users.destroy', $user) }}"
        method="POST"
        class="d-inline-block"
        onsubmit="return confirm('Xóa user này?');">
    @csrf
    @method('DELETE')
    <button style="padding: 0.5rem 2rem;" class="btn-mimi nut-do">Xóa user</button>
  </form>

  <hr>

  {{-- Nav tabs --}}
  <ul class="nav nav-tabs" id="userTabs" role="tablist">
    <li class="nav-item">
      <button class="nav-link active"
              data-bs-toggle="tab"
              data-bs-target="#orders">
        Đơn hàng
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link"
              data-bs-toggle="tab"
              data-bs-target="#help-requests">
        Yêu cầu trợ giúp
      </button>
    </li>
  </ul>

  <div class="tab-content mt-3">
    {{-- Orders Tab --}}
    <div class="tab-pane fade show active" id="orders">
      <h3>Đơn hàng của user</h3>
      @if($orders->isEmpty())
        <p>Chưa có đơn hàng nào.</p>
      @else
        <table class="table table-bordered">
          <thead class="table-light">
            <tr>
              <th>#Order</th>
              <th>Ngày</th>
              <th>Tổng</th>
              <th>Sản phẩm</th>
            </tr>
          </thead>
          <tbody>
            @foreach($orders as $o)
              <tr>
                <td>{{ $o->id }}</td>
                <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ number_format($o->total,0,',','.') }}₫</td>
                <td>
                  <ul class="mb-0">
                    @foreach($o->items as $it)
                      <li>
                        {{ $it->product->name }} × {{ $it->quantity }}
                        @if($it->options)
                          <ul>
                            @php
                              $vals = \App\Models\OptionValue::whereIn('id', $it->options)
                                         ->with('type')->get();
                            @endphp
                            @foreach($vals as $v)
                              <li>{{ $v->type->name }}: {{ $v->value }}</li>
                            @endforeach
                          </ul>
                        @endif
                      </li>
                    @endforeach
                  </ul>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>

    {{-- Help Requests Tab --}}
    <div class="tab-pane fade" id="help-requests">
      <h3>Yêu cầu trợ giúp</h3>
      @if($user->helpRequests->isEmpty())
        <p>Chưa có yêu cầu trợ giúp nào.</p>
      @else
        <table
          class="table table-bordered text-center table-tro-giup"
          style="table-layout:auto;"
        >
         

          <thead class="table-light">
            <tr>
              <th style="width:8%; !important">Họ tên</th>
              <th style="width:10%; !important">SĐT</th>
              <th style="width:29%; !important">Nội dung</th>
              <th style="width:29%; !important">Phản hồi</th>
              <th style="width:8%; !important">Trạng thái</th>
              <th style="width:9%; !important">Thời gian gửi</th>
              <th style="width:6%; !important">Hành động</th>
            </tr>
          </thead>
          <tbody>
            @foreach($user->helpRequests as $req)
              <tr>
                <td>{{ $req->name }}</td>
                <td>{{ $req->phone }}</td>
                <td>{{ $req->message }}</td>
                <td class="form-cell">
                  <form action="{{ route('admin.help_requests.update',$req) }}" method="POST">
                    @csrf
                    <textarea
                      name="response"
                      class="form-control form-control-sm auto-resize"
                      placeholder="Gõ phản hồi…"
                    >{{ $req->response }}</textarea>
                </td>
                <td>
                    <select name="status" class="form-select form-select-sm">
                      @foreach(\App\Models\HelpRequest::STATUSES as $st)
                        <option value="{{ $st }}" {{ $req->status=== $st ? 'selected':'' }}>{{ $st }}</option>
                      @endforeach
                    </select>
                </td>
                <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <button style = "font-size: 1rem; padding: 0.5rem 0.7rem;" class="btn-mimi nut-xanh-la btn-sm ">Lưu</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
  </div>
</div>
@endsection

{{-- === No more auto-resize script needed for height fill === --}}
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.auto-resize').forEach(textarea => {
      const adjust = () => {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
      };
      textarea.addEventListener('input', adjust);
      adjust();
    });
  });
</script>
@endpush
