@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="mb-4">Quản lý Sidebar</h1>
  <a href="{{ route('admin.sidebar-items.create') }}"
     class="btn btn-primary mb-3">
    Thêm Mục Cha Mới
  </a>

  <ul class="list-group">
    @foreach($items as $item)
      <li class="list-group-item">
        <div class="d-flex justify-content-between align-items-center">
          <strong>{{ $item->name }}</strong>
          <span>
            <a href="{{ route('admin.sidebar-items.edit', $item) }}"
               class="btn btn-sm btn-warning">Sửa</a>
            <form action="{{ route('admin.sidebar-items.destroy', $item) }}"
                  method="POST" class="d-inline"
                  onsubmit="return confirm('Xóa mục “{{ $item->name }}”?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger">Xóa</button>
            </form>
            {{-- nút Thêm Mục Con đã bỏ --}}
          </span>
        </div>

        @if($item->children->isNotEmpty())
          <ul class="list-group mt-2 ms-4">
            @foreach($item->children as $child)
              <li class="list-group-item d-flex justify-content-between">
                {{ $child->name }}
                <span>
                  <a href="{{ route('admin.sidebar-items.edit', $child) }}"
                     class="btn btn-sm btn-warning">Sửa</a>
                  <form action="{{ route('admin.sidebar-items.destroy', $child) }}"
                        method="POST" class="d-inline"
                        onsubmit="return confirm('Xóa mục con “{{ $child->name }}”?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Xóa</button>
                  </form>
                </span>
              </li>
            @endforeach
          </ul>
        @endif
      </li>
    @endforeach
  </ul>
</div>
@endsection
