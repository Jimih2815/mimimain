@extends('layouts.admin')

@section('content')
<div class="container-fluid">
  <h1 class="mb-4">Quản lý Widget Placements</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <a href="{{ route('admin.placements.create') }}" class="btn btn-primary mb-3">
    <i class="fa fa-plus"></i> Tạo Placement mới
  </a>

  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>Region</th>
        <th>Widget Slug</th>
        <th>Collection</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($pl as $p)
      <tr>
        <td>{{ $p->region }}</td>
        <td>{{ $p->widget->slug }}</td>
        <td>{{ $p->widget->collection?->name ?? '—' }}</td>
        <td class="d-flex gap-1">
          <a href="{{ route('admin.placements.edit', $p) }}"
             class="btn btn-sm btn-warning">Sửa</a>
          <form action="{{ route('admin.placements.destroy', $p) }}"
                method="POST" onsubmit="return confirm('Xóa placement?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger">Xóa</button>
          </form>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="4" class="text-center">Chưa có placement nào.</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <div class="d-flex justify-content-center">
    {{ $pl->links() }}
  </div>
</div>
@endsection
