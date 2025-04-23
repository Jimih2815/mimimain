@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">Quản lý Widgets</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <a href="{{ route('admin.widgets.create') }}" class="btn btn-primary mb-3">
    <i class="fa fa-plus"></i> Tạo Widget mới
  </a>

  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Slug</th>
        <th>Type</th>
        <th>Collection</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($widgets as $w)
      <tr>
        <td>{{ $w->id }}</td>
        <td>{{ $w->slug }}</td>
        <td>{{ ucfirst($w->type) }}</td>
        <td>{{ $w->collection?->name ?? '—' }}</td>
        <td class="d-flex gap-1">
          <a href="{{ route('admin.widgets.edit', $w) }}"
             class="btn btn-sm btn-warning">Sửa</a>
          <form action="{{ route('admin.widgets.destroy', $w) }}"
                method="POST" onsubmit="return confirm('Xóa widget?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger">Xóa</button>
          </form>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="text-center">Chưa có widget nào.</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <div class="d-flex justify-content-center">
    {{ $widgets->links() }}
  </div>
</div>
@endsection
