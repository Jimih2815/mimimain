@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="mb-4">Thêm Mục Cha Mới</h1>

  <form action="{{ route('admin.sidebar-items.store') }}" method="POST" id="sidebar-form">
    @csrf

    {{-- 1) Tên mục cha --}}
    <div class="mb-3">
      <label class="form-label">Tên mục</label>
      <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>

    {{-- 2) Thứ tự --}}
    <div class="mb-3">
      <label class="form-label">Thứ tự (số nhỏ lên trước)</label>
      <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order',0) }}">
    </div>

    {{-- 3) Section để chứa các mục con --}}
    <div class="mb-3">
      <label class="form-label">Mục con</label>
      <div id="children-wrapper">
        {{-- nếu có lỗi validate từ server, giữ lại các giá trị cũ --}}
        @if(old('children'))
          @foreach(old('children') as $i => $oldChild)
            <div class="child-item input-group mb-2">
              <input type="text"
                     name="children[{{ $i }}][name]"
                     class="form-control"
                     placeholder="Tên mục con"
                     value="{{ $oldChild['name'] }}" required>
              <select name="children[{{ $i }}][collection_id]"
                      class="form-select" required>
                <option value="">— Chọn Collection —</option>
                @foreach($collections as $id => $col)
                  <option value="{{ $id }}"
                    {{ $oldChild['collection_id']==$id ? 'selected' : '' }}>
                    {{ $col }}
                  </option>
                @endforeach
              </select>
              <button type="button" class="btn btn-outline-danger btn-remove-child">✕</button>
            </div>
          @endforeach
        @endif
      </div>
      <button type="button" id="btn-add-child" class="btn btn-sm btn-success">
        + Thêm mục con
      </button>
    </div>

    <button class="btn btn-primary">Tạo Mục Cha</button>
  </form>
</div>

{{-- Template ẩn để clone --}}
<template id="tpl-child">
  <div class="child-item input-group mb-2">
    <input type="text"
           name="__NAME__"
           class="form-control"
           placeholder="Tên mục con"
           required>
    <select name="__COLSELECT__"
            class="form-select" required>
      <option value="">— Chọn Collection —</option>
      @foreach($collections as $id => $col)
        <option value="{{ $id }}">{{ $col }}</option>
      @endforeach
    </select>
    <button type="button" class="btn btn-outline-danger btn-remove-child">✕</button>
  </div>
</template>

{{-- JS thêm/xóa mục con --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const wrapper = document.getElementById('children-wrapper');
  const tpl     = document.getElementById('tpl-child').innerHTML;
  let counter   = wrapper.children.length;

  document.getElementById('btn-add-child').addEventListener('click', () => {
    // build tên field dynamic: children[0]...[n]
    const nameInput      = `children[${counter}][name]`;
    const collectionInput= `children[${counter}][collection_id]`;
    let html = tpl
      .replace(/__NAME__/g, nameInput)
      .replace(/__COLSELECT__/g, collectionInput);
    wrapper.insertAdjacentHTML('beforeend', html);
    counter++;
  });

  // Delegate event cho nút xóa
  wrapper.addEventListener('click', e => {
    if (e.target.matches('.btn-remove-child')) {
      e.target.closest('.child-item').remove();
    }
  });
});
</script>
@endpush
@endsection
