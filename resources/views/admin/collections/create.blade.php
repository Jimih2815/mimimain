{{-- resources/views/admin/collections/create.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="trang-collection trang-sua-collection">
  <a href="{{ route('admin.collections.index') }}" class=" mb-5 nut-back">
      <i class="fas fa-arrow-left me-1"></i> Về Trang Collections
  </a>
  <h1 class="mb-4" style="color: #b83232; font-size: 3rem;">Tạo Bộ sưu tập</h1>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.collections.store') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label class="form-label">Tên Bộ Sưu Tập</label>
      <input name="name" value="{{ old('name') }}" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Slug</label>
      <input name="slug" value="{{ old('slug') }}" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Mô Tả</label>
      <textarea name="description" class="form-control">{{ old('description') }}</textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Chọn sản phẩm</label>
      <select id="products-select"
              name="products[]"
              class="form-select"
              multiple>
        @foreach($products as $p)
          <option value="{{ $p->id }}"
            {{ in_array($p->id, old('products', [])) ? 'selected' : '' }}>
            {{ $p->name }}
          </option>
        @endforeach
      </select>
      <small class="text-muted">Gõ để tìm, click vào tag × để bỏ chọn.</small>
    </div>

    <button class="btn-mimi nut-sua">Lưu</button>
    <a href="{{ route('admin.collections.index') }}" class="btn-mimi nut-xoa ms-1">Hủy</a>
  </form>
</div>
@endsection

@push('styles')
  <!-- TomSelect CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
@endpush

@push('scripts')
  <!-- TomSelect JS -->
  <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      new TomSelect('#products-select', {
        plugins: ['remove_button', 'no_backspace_delete'],
        persist: false,
        create: false,
        maxItems: null,              // cho phép chọn nhiều
        placeholder: 'Tìm sản phẩm…',
        dropdownDirection: 'bottom'
      });
    });
  </script>
@endpush
