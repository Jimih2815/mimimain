@extends('layouts.admin')

@section('content')
<div class="trang-sidebar">
  <h1 class="mb-4" style="color: #b83232; font-size: 3rem;">Quản lý Sidebar</h1>
  <a href="{{ route('admin.sidebar-items.create') }}"
     class="btn-mimi nut-them-slide mb-3">
    Thêm Mục Cha Mới
  </a>

  <ul class="list-group">
    @foreach($items as $item)
      <li class="list-group-item">
        <div class="d-flex justify-content-between align-items-center">
          <strong>{{ $item->name }}</strong>
          <span>
            <a href="{{ route('admin.sidebar-items.edit', $item) }}"
               class="btn-mimi nut-sua">Sửa</a>
            <form action="{{ route('admin.sidebar-items.destroy', $item) }}"
                  method="POST" class="d-inline"
                  onsubmit="return confirm('Xóa mục “{{ $item->name }}”?')">
              @csrf @method('DELETE')
              <button class="btn-mimi nut-xoa">Xóa</button>
            </form>
            {{-- nút Thêm Mục Con đã bỏ --}}
          </span>
        </div>

        @if($item->children->isNotEmpty())
          <ul class="list-group mt-2 ms-4">
            @foreach($item->children as $child)
              <li class="list-group-item group-trong d-flex justify-content-between">
                {{ $child->name }}
                <span>
                  <!-- <a href="{{ route('admin.sidebar-items.edit', $child) }}"
                     class="btn btn-sm btn-warning">Sửa</a> -->
                  <form action="{{ route('admin.sidebar-items.destroy', $child) }}"
                        method="POST" class="d-inline"
                        onsubmit="return confirm('Xóa mục con “{{ $child->name }}”?')">
                    @csrf @method('DELETE')
                    <button class="nut-xoa-muc-con">Xóa Mục Con</button>
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
