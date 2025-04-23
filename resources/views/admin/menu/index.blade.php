{{-- resources/views/admin/menu/index.blade.php (v2 – Section → Group → Products) --}}
@extends('layouts.app')
@section('title','Admin – Mega-Menu')

@php
  // lấy sẵn danh sách product cho dropdown
  $products = \App\Models\Product::orderBy('name')->get();
@endphp

@section('content')
<div class="container py-4">
  <h2 class="mb-4">Quản lý Mega-Menu Header</h2>

  {{-- 1️⃣  Tạo Section mới --}}
  <form action="{{ route('admin.menu.section.store') }}" method="POST" class="d-flex gap-2 mb-4">
    @csrf
    <input name="name" class="form-control w-25" placeholder="Tên section (VD: Đèn Ngủ)" required>
    <button class="btn btn-primary">+ Section</button>
  </form>

  {{-- 2️⃣ Tabs các section --}}
  <ul class="nav nav-tabs mb-3">
    @foreach($sections as $i => $sec)
      <li class="nav-item" role="presentation">
        <button class="nav-link {{ $i===0?'active':'' }}" data-bs-toggle="tab" data-bs-target="#tab-sec-{{ $sec->id }}" type="button" role="tab">{{ $sec->name }}</button>
      </li>
    @endforeach
  </ul>

  {{-- 3️⃣ Nội dung từng section --}}
  <div class="tab-content">
    @foreach($sections as $i => $sec)
      <div id="tab-sec-{{ $sec->id }}" class="tab-pane fade {{ $i===0?'show active':'' }}" role="tabpanel">

        {{-- A) Chỉnh sửa Section --}}
        <form action="{{ route('admin.menu.section.update',$sec) }}" method="POST" class="d-flex gap-2 align-items-center mb-3">
          @csrf @method('PUT')
          <input name="name" class="form-control w-25" value="{{ $sec->name }}" required>
          <input name="sort_order" type="number" class="form-control w-15" value="{{ $sec->sort_order }}">
          <button class="btn btn-success btn-sm">Lưu Section</button>
        </form>
        <form action="{{ route('admin.menu.section.destroy',$sec) }}" method="POST" class="mb-4">
          @csrf @method('DELETE')
          <button class="btn btn-danger btn-sm" onclick="return confirm('Xoá section?')">Xoá Section</button>
        </form>

        {{-- B) Danh sách Group (mục) --}}
        @foreach($sec->groups as $grp)
          <div class="border rounded p-3 mb-3">
            {{-- B1) Thông tin Group --}}
            <form action="{{ route('admin.menu.group.update',$grp) }}" method="POST" class="d-flex gap-2 align-items-center mb-2">
              @csrf @method('PUT')
              <input name="title" class="form-control w-25" value="{{ $grp->title }}" required>
              <input name="sort_order" type="number" class="form-control w-15" value="{{ $grp->sort_order }}">
              <button class="btn btn-success btn-sm">Lưu Mục</button>
            </form>
            <form action="{{ route('admin.menu.group.destroy',$grp) }}" method="POST" class="mb-3">
              @csrf @method('DELETE')
              <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Xoá mục?')">Xoá Mục</button>
            </form>

            {{-- B2) Danh sách sản phẩm trong Group --}}
            <ul class="list-group mb-2">
              @foreach($grp->products as $p)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span>{{ $p->name }}</span>
                  <form action="{{ route('admin.menu.group.product.remove',['group'=>$grp->id,'pid'=>$p->id]) }}" method="POST" class="m-0">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">&times;</button>
                  </form>
                </li>
              @endforeach
              @if($grp->products->isEmpty())
                <li class="list-group-item text-muted">Chưa có sản phẩm</li>
              @endif
            </ul>

            {{-- B3) Thêm sản phẩm vào Group --}}
            <form action="{{ route('admin.menu.group.product.add',$grp) }}" method="POST" class="d-flex gap-2 align-items-end">
              @csrf
              <select name="product_id" class="form-select form-select-sm w-25" required>
                <option value="" selected>– Chọn sản phẩm –</option>
                @foreach($products as $prod)
                  @if(!$grp->products->contains($prod->id))
                    <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                  @endif
                @endforeach
              </select>
              <button class="btn btn-primary btn-sm">+ Thêm</button>
            </form>
          </div>
        @endforeach

        {{-- C) Thêm Group mới --}}
        <form action="{{ route('admin.menu.group.store',$sec) }}" method="POST" class="d-flex gap-2 mt-3">
          @csrf
          <input name="title" class="form-control w-25" placeholder="Tên mục (VD: Bestseller)" required>
          <button class="btn btn-primary btn-sm">+ Mục</button>
        </form>

      </div>
    @endforeach
  </div>
</div>
@endsection