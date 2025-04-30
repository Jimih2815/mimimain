@extends('layouts.admin')

@section('content')
<style>
  /* ==== Style chung cho trang Quản lý Người dùng ==== */
  /* th nền xanh, viền trắng 2px, chữ trắng, căn giữa */
  .table.table-bordered.table-hover th {
    background-color: #4ab3af !important;
    border: 2px solid #fff      !important;
    color: #fff                 !important;
    text-align: center;
    vertical-align: middle;
  }
  /* tất cả td căn giữa */
  .table.table-bordered.table-hover td {
    text-align: center;
    vertical-align: middle;
  }
  /* nếu có td[colspan], canh trái và xoá padding trái */
  .table.table-bordered.table-hover td[colspan] {
    text-align: left;
    padding-left: 0 !important;
  }
</style>


<div class="container-fluid">
    <h1 class="mb-4 style="color: #b83232; font-size: 3rem;"">Quản lý Người dùng</h1>

    <!-- @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif -->

    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>ID</th><th>Name</th><th>Email</th><th>Đăng ký</th><th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
        <tr>
          <td>{{ $u->id }}</td>
          <td>{{ $u->name }}</td>
          <td>{{ $u->email }}</td>
          <td>{{ $u->created_at->format('d/m/Y H:i') }}</td>
          <td class="d-flex gap-1">
            <a href="{{ route('admin.users.show', $u) }}"
               class="btn btn-sm btn-info">Xem</a>
            <form action="{{ route('admin.users.resetPassword', $u) }}"
                  method="POST" onsubmit="return confirm('Reset mật khẩu?')">
              @csrf
              <button class="btn btn-sm btn-warning">Reset PW</button>
            </form>
            <form action="{{ route('admin.users.destroy', $u) }}"
                  method="POST" onsubmit="return confirm('Xóa user?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-danger">Xóa</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center">Chưa có user nào.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="d-flex justify-content-center">
      {{ $users->links() }}
    </div>
</div>
@endsection
