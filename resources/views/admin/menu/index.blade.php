{{-- resources/views/admin/menu/index.blade.php --}}
@extends('layouts.app')
@section('title','Admin – Mega‑Menu')

@php
  /* Lấy danh sách sản phẩm 1 lần để hiển thị dropdown chọn nhanh */
  $products = \App\Models\Product::orderBy('name')->get();
@endphp

@section('content')
<div class="container py-4">
  <h2 class="mb-4">Quản lý Mega‑Menu Header</h2>

  {{-- 1️⃣  Tạo Section mới – chỉ cần tên, slug tự sinh --}}
  <form action="{{ route('admin.menu.section.store') }}" method="POST" class="d-flex gap-2 mb-4">
    @csrf
    <input name="name" class="form-control w-25" placeholder="Tên section (VD: Đèn Ngủ)">
    <button class="btn btn-primary">+ Section</button>
  </form>

  {{-- 2️⃣ Tabs các section --}}
  <ul class="nav nav-tabs mb-3" id="menuTab" role="tablist">
    @foreach($sections as $k => $sec)
      <li class="nav-item" role="presentation">
        <button class="nav-link {{ $k===0?'active':'' }}"
                data-bs-toggle="tab"
                data-bs-target="#tab-sec-{{ $sec->id }}"
                type="button" role="tab">
          {{ $sec->name }}
        </button>
      </li>
    @endforeach
  </ul>

  {{-- 3️⃣ Pane chi tiết từng section --}}
  <div class="tab-content" id="menuTabContent">
    @foreach($sections as $k => $sec)
      <div id="tab-sec-{{ $sec->id }}" class="tab-pane fade {{ $k===0?'show active':'' }}" role="tabpanel">

        {{-- 3a) Thông tin Section (slug ẩn) --}}
        <form action="{{ route('admin.menu.section.update',$sec) }}" method="POST" class="d-flex gap-2 align-items-center mb-3">
          @csrf @method('PUT')
          <input name="name"  class="form-control w-25"  value="{{ $sec->name }}">
          <input name="sort_order" type="number" class="form-control w-15" value="{{ $sec->sort_order }}">
          <button class="btn btn-success btn-sm">Lưu Section</button>
        </form>

        {{-- Xoá Section --}}
        <form action="{{ route('admin.menu.section.destroy',$sec) }}" method="POST" class="mb-4">
          @csrf @method('DELETE')
          <button class="btn btn-danger btn-sm" onclick="return confirm('Xoá section?')">Xoá Section</button>
        </form>

        {{-- 3b) Danh sách Item --}}
        <ul class="list-group mb-3">
          @foreach($sec->items as $item)
            <li class="list-group-item">
              <form action="{{ route('admin.menu.item.update',$item) }}" method="POST" class="d-flex gap-2 align-items-center">
                @csrf @method('PUT')
                <input name="label" class="form-control w-25" value="{{ $item->label }}">
                <input name="url"   class="form-control w-25" value="{{ $item->url }}">
                <input name="sort_order" type="number" class="form-control w-15" value="{{ $item->sort_order }}">
                <button class="btn btn-success btn-sm">Lưu</button>
              </form>
              <form action="{{ route('admin.menu.item.destroy',$item) }}" method="POST" class="d-inline ms-2">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Xoá item?')">&times;</button>
              </form>
            </li>
          @endforeach
        </ul>

        {{-- 3c) Thêm Item mới – chọn nhanh sản phẩm --}}
        <form action="{{ route('admin.menu.item.store',$sec) }}" method="POST" class="d-flex gap-2 align-items-end" id="addItemForm-{{ $sec->id }}">
          @csrf
          <div class="w-25">
            <label class="form-label small mb-1">Chọn sản phẩm</label>
            <select class="form-select form-select-sm product-select" data-sec="{{ $sec->id }}">
              <option value="" selected>– Sản phẩm –</option>
              @foreach($products as $p)
                <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-url="{{ route('products.show',$p->slug ?? $p->id) }}">
                  {{ $p->name }}
                </option>
              @endforeach
            </select>
          </div>
          {{-- label & url sẽ được auto điền --}}
          <input type="text" name="label" class="form-control form-control-sm w-25" placeholder="Nhãn" required>
          <input type="text" name="url"   class="form-control form-control-sm w-25" placeholder="/duong-dan" required>
          <button class="btn btn-primary btn-sm">+ Item</button>
        </form>
      </div>
    @endforeach
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Auto-fill label + url khi chọn sản phẩm
  document.querySelectorAll('.product-select').forEach(sel => {
    sel.addEventListener('change', e => {
      const opt   = e.target.selectedOptions[0];
      const form  = document.getElementById('addItemForm-' + e.target.dataset.sec);
      if (!opt || !form) return;
      form.querySelector('input[name="label"]').value = opt.dataset.name || '';
      form.querySelector('input[name="url"]').value   = opt.dataset.url  || '';
    });
  });
</script>
@endpush
