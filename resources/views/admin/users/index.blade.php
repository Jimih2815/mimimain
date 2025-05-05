{{-- resources/views/admin/user/index.blade.php --}}
@extends('layouts.admin')

@push('styles')
<style>
  /* Nav tabs styling */
  .nav-link {
    color: #4ab3af;
    font-size: 1.2rem;
    margin-right: 1rem;
  }
  .nav-link.active {
    color: #333333;
    font-weight: bold;
  }

  /* Table header */
  .table-tro-giup thead th,
  .table-bordered.table-hover th {
    background-color: #4ab3af !important;
    color: #fff !important;
    text-align: center;
    vertical-align: middle;
  }

  /* Center all cells */
  .table-bordered.table-hover td {
    text-align: center;
    vertical-align: middle;
  }

  /* Left-align any colspan cells */
  .table-bordered.table-hover td[colspan] {
    text-align: left;
    padding-left: 0 !important;
  }

  /* Help-requests table: responsive columns */
  .table-tro-giup {
    table-layout: auto !important;
    width: 100%;
  }
  .table-tro-giup td {
    white-space: normal;
    word-break: break-word;
    vertical-align: top;
    padding: .25rem !important;
  }
  /* Auto-resize textarea */
  .auto-resize {
    width: 100%;
    box-sizing: border-box;
    resize: none;
    overflow: hidden;
    font-size: .875rem;
    line-height: 1.4;
    border: none;
  }
  /* Form-cell wrapper for full-height textarea */
  .table-tro-giup td.form-cell {
    position: relative;
    padding: 0 !important;
  }
  .table-tro-giup td.form-cell .auto-resize {
    position: absolute !important;
    top: 0; left: 0; right: 0; bottom: 0;
    margin: 0 !important;
    height: 100% !important;
    overflow-y: auto !important;
    box-sizing: border-box;
  }
</style>
@endpush

@section('content')
<div class="container-fluid trang-admin-user">
  <h1 class="mb-4">Quản lý Người dùng & Yêu cầu trợ giúp</h1>

  {{-- Nav tabs --}}
  <ul class="nav nav-tabs" id="adminTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active"
              data-bs-toggle="tab"
              data-bs-target="#tab-users"
              type="button" role="tab">
        Người dùng
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link"
              data-bs-toggle="tab"
              data-bs-target="#tab-help"
              type="button" role="tab">
        Yêu cầu trợ giúp
      </button>
    </li>
  </ul>

  <div class="tab-content mt-3">
    {{-- Tab: Người dùng --}}
    <div class="tab-pane fade show active" id="tab-users" role="tabpanel">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Đăng ký</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            <tr>
              <td>{{ $u->id }}</td>
              <td>{{ $u->name }}</td>
              <td>{{ $u->email }}</td>
              <td>{{ $u->created_at->format('d/m/Y H:i') }}</td>
              <td class="d-flex gap-1 justify-content-center">
                <a href="{{ route('admin.users.show', $u) }}"
                   class="btn btn-sm btn-info">Xem</a>
                <form action="{{ route('admin.users.resetPassword', $u) }}"
                      method="POST" onsubmit="return confirm('Reset mật khẩu?')">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-warning">Reset PW</button>
                </form>
                <form action="{{ route('admin.users.destroy', $u) }}"
                      method="POST" onsubmit="return confirm('Xóa user?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger">Xóa</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center">Chưa có user nào.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div class="d-flex justify-content-center">
        {{ $users->links() }}
      </div>
    </div>

    {{-- Tab: Yêu cầu trợ giúp --}}
    <div class="tab-pane fade" id="tab-help" role="tabpanel">
      <table class="table table-bordered text-center table-tro-giup" style="table-layout:auto;">
        <colgroup>
          <col style="width:8%">
          <col style="width:10%">
          <col>
          <col>
          <col style="width:8%">
          <col style="width:9%">
          <col style="width:6%">
        </colgroup>
        <thead>
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
          @foreach(\App\Models\HelpRequest::with('user')->orderBy('created_at','desc')->get() as $req)
            <tr>
              <td>{{ $req->user->name }}</td>
              <td>{{ $req->phone }}</td>
              <td>{{ $req->message }}</td>
              <td class="form-cell">
                <form action="{{ route('admin.help_requests.update', $req) }}" method="POST">
                  @csrf
                  <textarea name="response"
                            class="form-control form-control-sm auto-resize">{{ $req->response }}</textarea>
              </td>
              <td>
                <select name="status" class="form-select form-select-sm">
                  @foreach(\App\Models\HelpRequest::STATUSES as $st)
                    <option value="{{ $st }}" {{ $req->status === $st ? 'selected' : '' }}>
                      {{ $st }}
                    </option>
                  @endforeach
                </select>
              </td>
              <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
              <td>
                <button class="btn-mimi nut-xanh-la btn-sm">Lưu</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

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
