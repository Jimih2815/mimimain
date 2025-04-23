@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">Quản lý Bộ sưu tập</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <a href="{{ route('admin.collections.create') }}" class="btn btn-primary mb-3">
    <i class="fa fa-plus"></i> Tạo bộ sưu tập mới
  </a>

  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Slug</th>
        <th>Số SP</th>
        <th>Thao tác</th>
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
          <a href="{{ route('collections.show', $col->slug) }}" class="btn btn-sm btn-info">Xem</a>
          <a href="{{ route('admin.collections.edit', $col) }}" class="btn btn-sm btn-warning">Sửa</a>
          <form action="{{ route('admin.collections.destroy', $col) }}" method="POST" onsubmit="return confirm('Xóa bộ sưu tập này?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger">Xóa</button>
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
