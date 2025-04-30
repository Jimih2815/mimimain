@extends('layouts.admin')

@section('content')
<div class="trang-collection">
  <h1 class="mb-4" style=" color: #b83232; font-size: 3rem;">Quản lý Bộ sưu tập</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <a href="{{ route('admin.collections.create') }}" class="btn-mimi tao-bo-suu-tap mb-3">
    <i class="fa fa-plus"></i> Tạo bộ sưu tập mới
  </a>

  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th style="width: 2rem;">ID</th>
        <th>Tên Bộ Sưu Tập</th>
        <th>Slug</th>
        <th>Số Sản phẩm</th>
        <th style ="width: 12rem;">Thao tác</th>
      </tr>
    </thead>
    <tbody>
      @forelse($cols as $col)
      <tr>
        <td>{{ $col->id }}</td>
        <td>{{ $col->name }}</td>
        <td>{{ $col->slug }}</td>
        <td>{{ $col->products()->count() }}</td>
        <td class="d-flex gap-1">
          <a href="{{ route('collections.show', $col->slug) }}" class="btn-mimi nut-xem">Xem</a>
          <a href="{{ route('admin.collections.edit', $col) }}" class="btn-mimi nut-sua">Sửa</a>
          <form action="{{ route('admin.collections.destroy', $col) }}" method="POST" onsubmit="return confirm('Xóa bộ sưu tập này?')">
            @csrf
            @method('DELETE')
            <button class="btn-mimi nut-xoa">Xóa</button>
          </form>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="text-center">Chưa có bộ sưu tập nào.</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <div class="d-flex justify-content-center">
    {{ $cols->links() }}
  </div>
</div>
@endsection
