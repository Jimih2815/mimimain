{{-- resources/views/admin/headers/index.blade.php --}}
@extends('layouts.app')

@section('title','Admin — Quản lý Mega‑Menu Header')

@section('content')
<div class="container py-4">
  <h2 class="mb-4">Quản lý Mega‑Menu Header</h2>

  {{-- Nav‑tabs cho mỗi Category --}}
  <ul class="nav nav-tabs mb-3" id="catTab" role="tablist">
    @foreach($categories as $i => $cat)
      <li class="nav-item" role="presentation">
        <button
          class="nav-link {{ $i===0?'active':'' }}"
          id="cat-{{ $cat->id }}-tab"
          data-bs-toggle="tab"
          data-bs-target="#cat-{{ $cat->id }}"
          type="button"
          role="tab"
          aria-controls="cat-{{ $cat->id }}"
          aria-selected="{{ $i===0?'true':'false' }}"
        >
          {{ $cat->name }}
        </button>
      </li>
    @endforeach
  </ul>

  <div class="tab-content" id="catTabContent">
    @foreach($categories as $i => $cat)
      <div
        class="tab-pane fade {{ $i===0?'show active':'' }}"
        id="cat-{{ $cat->id }}"
        role="tabpanel"
        aria-labelledby="cat-{{ $cat->id }}-tab"
      >
        <h4 class="mb-3">Category: {{ $cat->name }}</h4>

        {{-- Danh sách Header Box --}}
        <ul class="list-group mb-4">
          @foreach($cat->headers as $header)
            <li class="list-group-item">

              {{-- Sửa tiêu đề & thứ tự --}}
              <form
                action="{{ route('admin.headers.update', $header) }}"
                method="POST"
                class="d-flex gap-2 align-items-center mb-3"
              >
                @csrf @method('PUT')
                <input
                  type="text"
                  name="title"
                  value="{{ $header->title }}"
                  class="form-control form-control-sm w-25"
                  placeholder="Tiêu đề Header"
                >
                <input
                  type="number"
                  name="sort_order"
                  value="{{ $header->sort_order }}"
                  class="form-control form-control-sm w-10"
                  step="1"
                  placeholder="Thứ tự"
                >
                <button class="btn btn-success btn-sm">Lưu</button>
              </form>

              {{-- Danh sách sản phẩm đã thêm --}}
              <ul class="list-unstyled mb-2">
                @foreach($header->products as $prod)
                  <li class="d-flex justify-content-between align-items-center small py-1">
                    <span>{{ $prod->name }}</span>
                    <form
                      action="{{ route('admin.headers.product.remove', ['header'=>$header->id,'pid'=>$prod->id]) }}"
                      method="POST"
                      style="margin:0;"
                    >
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger">&times;</button>
                    </form>
                  </li>
                @endforeach
                @if($header->products->isEmpty())
                  <li class="text-muted small">Chưa có sản phẩm nào</li>
                @endif
              </ul>

              {{-- Thêm 1 sản phẩm --}}
              <form
                action="{{ route('admin.headers.product.add', $header) }}"
                method="POST"
                class="d-flex gap-2"
              >
                @csrf
                <select name="product_id" class="form-select form-select-sm w-50">
                  <option value="">-- Chọn sản phẩm --</option>
                  @foreach($products as $p)
                    @if(! $header->products->contains($p->id))
                      <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endif
                  @endforeach
                </select>
                <button class="btn btn-primary btn-sm">+</button>
              </form>

              {{-- Xóa Header --}}
              <form
                action="{{ route('admin.headers.destroy', $header) }}"
                method="POST"
                class="mt-2"
              >
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">Xóa Header</button>
              </form>

            </li>
          @endforeach
        </ul>

        {{-- Thêm Header mới --}}
        <form action="{{ route('admin.headers.store') }}" method="POST" class="d-flex gap-2">
          @csrf
          <input type="hidden" name="category_id" value="{{ $cat->id }}">
          <input
            type="text"
            name="title"
            class="form-control form-control-sm w-25"
            placeholder="Tiêu đề Header mới"
          >
          <button class="btn btn-primary btn-sm">Thêm Header</button>
        </form>
      </div>
    @endforeach
  </div>
</div>
@endsection
