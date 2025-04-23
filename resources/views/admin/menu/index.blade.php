{{-- resources/views/admin/menu/index.blade.php --}}
@extends('layouts.app')
@section('title','Admin – Mega-Menu')

@section('content')
<div class="container py-4">
  <h2 class="mb-4">Quản lý Mega-Menu Header</h2>

  {{-- Tạo Section mới --}}
  <form action="{{ route('admin.menu.section.store') }}" method="POST" class="d-flex gap-2 mb-4">
    @csrf
    <input name="name" class="form-control w-25" placeholder="Tên section (VD: Đèn Ngủ)">
    <input name="slug" class="form-control w-25" placeholder="Slug (den-ngu)">
    <button class="btn btn-primary">+ Section</button>
  </form>

  {{-- Nav-tabs --}}
  <ul class="nav nav-tabs mb-3" id="menuTab" role="tablist">
    @foreach($sections as $k => $sec)
      <li class="nav-item" role="presentation">
        <button class="nav-link {{ $k===0?'active':'' }}"
                data-bs-toggle="tab"
                data-bs-target="#tab-sec-{{ $sec->id }}"  {{-- tránh trùng ID với header --}}
                type="button"
                role="tab">
          {{ $sec->name }}
        </button>
      </li>
    @endforeach
  </ul>

  <div class="tab-content" id="menuTabContent">
    @foreach($sections as $k => $sec)
      <div id="tab-sec-{{ $sec->id }}"  {{-- prefix tab- để unique --}}
           class="tab-pane fade {{ $k===0?'show active':'' }}"
           role="tabpanel">

        {{-- Thông tin Section --}}
        <form action="{{ route('admin.menu.section.update',$sec) }}" method="POST" class="d-flex gap-2 align-items-center mb-3">
          @csrf @method('PUT')
          <input name="name"  class="form-control w-25"  value="{{ $sec->name }}">
          <input name="slug"  class="form-control w-25"  value="{{ $sec->slug }}">
          <input name="sort_order" type="number" class="form-control w-15" value="{{ $sec->sort_order }}">
          <button class="btn btn-success btn-sm">Lưu Section</button>
        </form>

        {{-- Xoá Section --}}
        <form action="{{ route('admin.menu.section.destroy',$sec) }}" method="POST" class="mb-4">
          @csrf @method('DELETE')
          <button class="btn btn-danger btn-sm" onclick="return confirm('Xoá section?')">Xoá Section</button>
        </form>

        {{-- Danh sách Item --}}
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
              <form action="{{ route('admin.menu.item.destroy',$item) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Xoá item?')">&times;</button>
              </form>
            </li>
          @endforeach
        </ul>

        {{-- Thêm Item mới --}}
        <form action="{{ route('admin.menu.item.store',$sec) }}" method="POST" class="d-flex gap-2">
          @csrf
          <input name="label" class="form-control w-25" placeholder="Nhãn">
          <input name="url"   class="form-control w-25" placeholder="/duong-dan">
          <button class="btn btn-primary btn-sm">+ Item</button>
        </form>
      </div>
    @endforeach
  </div>
</div>
@endsection