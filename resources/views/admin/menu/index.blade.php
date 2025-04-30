{{-- resources/views/admin/menu/index.blade.php (v2 – Section → Group → Products) --}}
@extends('layouts.admin')
@section('title','Admin – Mega-Menu')

@php
  // lấy sẵn danh sách product cho dropdown
  $products = \App\Models\Product::orderBy('name')->get();
@endphp

@push('styles')
  <!-- TomSelect CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
@endpush

@section('content')
<div class="trang-sua-menu">
  <h2 class="mb-4" style="color: #b83232; font-size: 3rem;">Quản lý Header</h2>

  {{-- ★ Preview Mega-Menu ★ --}}
  <div class="mega-menu-preview position-relative text-center py-3 bg-white mb-5">
    <ul class="nav justify-content-center">
      @foreach($sections as $section)
        <li class="nav-item dropdown position-static mx-2">
          <span class="nav-link px-3 fw-semibold text-dark">{{ $section->name }}</span>
          <div class="dropdown-menu p-4 mega-menu-preview__dropdown">
            <div class="row">
              @forelse($section->groups as $group)
                <div class="col-6 col-md-3 mb-3">
                  <h6 class="fw-bold text-uppercase mb-2">{{ $group->title }}</h6>
                  <ul class="list-unstyled">
                    @forelse($group->products as $p)
                      <li><span class="dropdown-item px-0 text-muted">{{ $p->name }}</span></li>
                    @empty
                      <li><small class="text-muted">Chưa có SP</small></li>
                    @endforelse
                  </ul>
                </div>
              @empty
                <div class="col-12 text-muted">Chưa có mục nào</div>
              @endforelse
            </div>
          </div>
        </li>
      @endforeach
      <li class="nav-item mx-2">
        <span class="nav-link px-3 text-dark">TOÀN BỘ</span>
      </li>
    </ul>
  </div>

  {{-- 1️⃣  Tạo Section mới --}}
  <form action="{{ route('admin.menu.section.store') }}" method="POST" class="d-flex gap-2 mb-4">
    @csrf
    {{-- Giữ tab sau khi tạo --}}
    <input type="hidden" name="active" value="{{ old('active', optional($sections->first())->id) }}">
    <input name="name" class="form-control w-25" placeholder="Tên section (VD: Đèn Ngủ)" required>
    <button class="nut-them-section btn-mimi">+ Section</button>
  </form>

  @php
    // Xác định tab active (mặc định là section đầu tiên nếu chưa có old input)
    $activeTab = old('active', optional($sections->first())->id);
  @endphp

  {{-- 2️⃣ Tabs các section --}}
  <ul class="nav nav-tabs mb-3">
    @foreach($sections as $i => $sec)
      <li class="nav-item" role="presentation">
        <button
          class="nav-link {{ $sec->id == $activeTab ? 'active' : '' }}"
          data-bs-toggle="tab"
          data-bs-target="#tab-sec-{{ $sec->id }}"
          type="button"
          role="tab"
        >{{ $sec->name }}</button>
      </li>
    @endforeach
  </ul>

  {{-- 3️⃣ Nội dung từng section --}}
  <div class="tab-content">
    @foreach($sections as $i => $sec)
      <div
        id="tab-sec-{{ $sec->id }}"
        class="tab-pane fade {{ $sec->id == $activeTab ? 'show active' : '' }}"
        role="tabpanel"
      >

        {{-- A) Chỉnh sửa Section --}}
        <form action="{{ route('admin.menu.section.update',$sec) }}"
              method="POST"
              class="d-flex gap-2 align-items-center mb-3">
          @csrf
          <!-- <p style="font-weight: 800; width: 12rem; margin: 0;">Tên Section</p> -->
          <input type="hidden" name="active" value="{{ $sec->id }}">
          @method('PUT')
          <input name="name" class="form-control" value="{{ $sec->name }}" required>
      {{-- Chọn Collection liên kết --}}
        <select name="collection_id" class="form-select w-25">
          <option value="">– Không liên kết –</option>
         @foreach($collections as $col)
            <option value="{{ $col->id }}"
              {{ $sec->collection_id == $col->id ? 'selected' : '' }}>
              {{ $col->name }}
            </option>
          @endforeach
        </select>
          <!-- <p style="font-weight: 800; width: 12rem; margin: 0;">Thứ tự</p> -->
          <input name="sort_order" style = "width:10%;" type="number" class="form-control" value="{{ $sec->sort_order }}">
          <button class="btn-mimi nut-luu">Lưu Section</button>
        </form>
        <form action="{{ route('admin.menu.section.destroy',$sec) }}"
              method="POST"
              class="mb-4 d-flex justify-content-end">
          @csrf
          <input type="hidden" name="active" value="{{ $sec->id }}">
          @method('DELETE')
          <button class="btn-mimi nut-xoa" onclick="return confirm('Xoá section?')">Xoá Section</button>
        </form>

        {{-- B) Danh sách Group (mục) --}}
        @foreach($sec->groups as $grp)
          <div class="p-3 mb-3 khung-section">
            {{-- B1) Thông tin Group --}}
            <form action="{{ route('admin.menu.group.update',$grp) }}"
                  method="POST"
                  class="d-flex gap-2 align-items-center mb-2">
              @csrf
              <input type="hidden" name="active" value="{{ $sec->id }}">
              @method('PUT')
              <input name="title" class="form-control" value="{{ $grp->title }}" required>
              <input name="sort_order" style = "width:10%;" type="number " class="form-control" value="{{ $grp->sort_order }}">
              <button class="btn-mimi luu-muc">Lưu Mục</button>
            </form>
            <form action="{{ route('admin.menu.group.destroy',$grp) }}"
                  method="POST"
                  class="mb-3">
              @csrf
              <input type="hidden" name="active" value="{{ $sec->id }}">
              @method('DELETE')
              <button class="nut-xoa-muc" onclick="return confirm('Xoá mục?')">Xoá Mục</button>
            </form>

            {{-- B2) Danh sách sản phẩm trong Group --}}
            <ul class="list-group mb-2">
              @foreach($grp->products as $p)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span>{{ $p->name }}</span>
                  <form action="{{ route('admin.menu.group.product.remove',['group'=>$grp->id,'pid'=>$p->id]) }}"
                        method="POST"
                        class="m-0">
                    @csrf
                    <input type="hidden" name="active" value="{{ $sec->id }}">
                    @method('DELETE')
                    <button class="nut-x"><i class="fa-solid fa-trash-can"></i></button>
                  </form>
                </li>
              @endforeach
              @if($grp->products->isEmpty())
                <li class="list-group-item text-muted">Chưa có sản phẩm</li>
              @endif
            </ul>

            {{-- B3) Thêm sản phẩm vào Group --}}
            <form action="{{ route('admin.menu.group.product.add',$grp) }}"
                  method="POST"
                  class="d-flex gap-2 align-items-end">
              @csrf
              <input type="hidden" name="active" value="{{ $sec->id }}">
              <select name="product_id"
                      class="form-select form-select-sm w-25 select-searchable"
                      required>
                <option value="" selected>– Chọn sản phẩm –</option>
                @foreach($products as $prod)
                  @if(!$grp->products->contains($prod->id))
                    <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                  @endif
                @endforeach
              </select>
              <button class="btn-mimi nut-them">+ Thêm</button>
            </form>
          </div>
        @endforeach

        {{-- C) Thêm Group mới --}}
        <form action="{{ route('admin.menu.group.store',$sec) }}"
              method="POST"
              class="d-flex gap-2 mt-3 form-them-muc-cont">
          @csrf
          <input type="hidden" name="active" value="{{ $sec->id }}">
          <div class="form-them-muc me-3">  <input name="title"
                  class="form-control"
                  placeholder="Tên mục (VD: Bestseller)"
                  required> </div>
          <button class="btn-mimi nut-them-muc">+ Thêm Mục Mới</button>
        </form>

      </div>
    @endforeach
  </div>
</div>
@endsection

@push('scripts')
  <!-- TomSelect JS -->
  <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // 1. Khôi phục vị trí cuộn cũ
      const scrollPos = sessionStorage.getItem('menuScrollPos');
      if (scrollPos) {
        window.scrollTo(0, parseInt(scrollPos));
        sessionStorage.removeItem('menuScrollPos');
      }

      // 2. Init TomSelect như cũ
      document.querySelectorAll('.select-searchable').forEach(function(el) {
        new TomSelect(el, {
          create: false,
          maxItems:   1,
          searchField:['text'],
          placeholder:'Chọn sản phẩm…',
          dropdownDirection:'bottom'
        });
      });

      // 3. Trước mỗi submit form, lưu scrollY lại
      document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function() {
          sessionStorage.setItem('menuScrollPos', window.scrollY);
        });
      });
    });
  </script>
@endpush
