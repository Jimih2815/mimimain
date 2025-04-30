@extends('layouts.admin')

@section('content')
<div class="trang-sidebar">
  <h1 class="mb-4" style="color: #b83232; font-size: 3rem;">Sửa Mục Sidebar</h1>

  <form action="{{ route('admin.sidebar-items.update', $sidebarItem) }}"
        method="POST" id="sidebar-edit-form">
    @csrf @method('PUT')

    {{-- Tên mục cha --}}
    <div class="mb-3">
      <label class="form-label">Tên mục</label>
      <input type="text" name="name" class="form-control"
             value="{{ old('name', $sidebarItem->name) }}" required>
    </div>

    {{-- Thứ tự --}}
    <div class="mb-3">
      <label class="form-label">Thứ tự (số nhỏ lên trước)</label>
      <input type="number" name="sort_order" class="form-control"
             value="{{ old('sort_order', $sidebarItem->sort_order) }}">
    </div>

    {{-- Danh sách mục con --}}
    <div class="mb-3">
      <label class="form-label">Mục con</label>
      <div id="children-wrapper">
        @foreach($sidebarItem->children as $i => $child)
          <div class="child-item input-group mb-2" data-child-id="{{ $child->id }}">
            {{-- Hidden để biết id khi update --}}
            <input type="hidden" name="children[{{ $i }}][id]" value="{{ $child->id }}">

            <input type="text"
                   name="children[{{ $i }}][name]"
                   class="form-control"
                   placeholder="Tên mục con"
                   value="{{ old("children.$i.name", $child->name) }}"
                   required>

            <select name="children[{{ $i }}][collection_id]"
                    class="form-select" required>
              <option value="">— Chọn Collection —</option>
              @foreach($collections as $colId => $colName)
                <option value="{{ $colId }}"
                  {{ old("children.$i.collection_id", $child->collection_id)==$colId ? 'selected' : '' }}>
                  {{ $colName }}
                </option>
              @endforeach
            </select>

            <button type="button" class="btn btn-outline-danger btn-remove-child nut-x-xoa">
              ✕
            </button>
          </div>
        @endforeach
      </div>

        <button type="button" id="btn-add-child" class="btn-mimi nut-them-slide">
          + Mục con
        </button>
    </div>

    <div class="cap-nhat-cont"> <button class="btn-mimi nut-xoa-muc-con">Cập nhật</button> </div>
  </form>
</div>

{{-- Template clone cho mục con mới --}}
<template id="tpl-child">
  <div class="child-item input-group mb-2">
    <input type="hidden" name="__ID__" value="">
    <input type="text"
           name="__NAME__"
           class="form-control"
           placeholder="Tên mục con"
           required>
    <select name="__COLLECT__" class="form-select" required>
      <option value="">— Chọn Collection —</option>
      @foreach($collections as $colId => $colName)
        <option value="{{ $colId }}">{{ $colName }}</option>
      @endforeach
    </select>
    <button type="button" class="btn btn-outline-danger btn-remove-child">
      ✕
    </button>
  </div>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const wrapper = document.getElementById('children-wrapper');
  const tplHTML = document.getElementById('tpl-child').innerHTML;
  let counter   = wrapper.children.length;

  // Thêm mục con mới
  document.getElementById('btn-add-child').addEventListener('click', () => {
    const nameField      = `children[${counter}][name]`;
    const colField       = `children[${counter}][collection_id]`;
    const idField        = `children[${counter}][id]`;
    let html = tplHTML
               .replace(/__NAME__/g, nameField)
               .replace(/__COLLECT__/g, colField)
               .replace(/__ID__/g, idField);
    wrapper.insertAdjacentHTML('beforeend', html);
    counter++;
  });

  // Xóa mục con (cả cũ và mới)
  wrapper.addEventListener('click', e => {
    if (e.target.matches('.btn-remove-child')) {
      e.target.closest('.child-item').remove();
    }
  });
});
</script>
@endpush
@endsection
