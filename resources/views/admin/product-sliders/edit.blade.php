{{-- resources/views/admin/product-sliders/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="trang-slider-san-pham">
  <a href="{{ route('admin.product-sliders.index') }}" class="nut-quay-ve mb-3">
  <i class="fa-solid fa-chevron-left"></i> Quay về danh sách slider
  </a>
  <h1 class="mb-4" style="color: #b83232; font-size: 3rem;">Sửa Slider sản phẩm</h1>

  {{-- Hiển thị lỗi validation --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.product-sliders.update', $slider) }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Chọn product --}}
    <div class="mb-3">
      <label class="form-label">Sản phẩm</label>
      <select id="product-select"
              name="product_id"
              class="form-select select-searchable"
              required>
        @foreach($products as $p)
          <option value="{{ $p->id }}"
            @if(old('product_id', $slider->product_id)==$p->id) selected @endif>
            {{ $p->name }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- Ảnh --}}
    <div class="mb-3">
      <label class="form-label">Ảnh slider (nếu để trống sẽ lấy ảnh sản phẩm gốc)</label>
      <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    @if($slider->image)
      <div class="mb-3">
        <p>Ảnh hiện tại:</p>
        <img src="{{ asset('storage/'.$slider->image) }}" width="150" alt="">
      </div>
    @endif

    {{-- Thứ tự --}}
    <div class="mb-3">
      <label class="form-label">Thứ tự (sort_order)</label>
      <input name="sort_order"
             type="number"
             class="form-control"
             value="{{ old('sort_order', $slider->sort_order) }}">
    </div>

    <button class="btn-mimi nut-sua">Cập nhật</button>
    <a href="{{ route('admin.product-sliders.index') }}" class="btn-mimi nut-xoa">Hủy</a>
  </form>
</div>
@endsection

@push('styles')
  <!-- TomSelect CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
  <style>
    /* --- “Dẹp” wrapper của TomSelect, chỉ giữ lại ts-control --- */
    .ts-wrapper.form-select.select-searchable {
      border: none !important;
      background: transparent !important;
      padding: 0 !important;
    }
    /* Định lại khung hiển thị chính */
    .ts-control.form-select {
      border: 1px solid #ced4da;
      border-radius: .25rem;
      padding: .375rem .75rem;
      background: #fff;
    }
    /* Cho dropdown bám full width */
    .ts-dropdown {
      position: absolute;
      top: 100%;
      left: 0;
      width: 100%;
      z-index: 1100;
    }
  </style>
@endpush

@push('scripts')
  <!-- TomSelect JS -->
  <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      new TomSelect('#product-select', {
        create: false,
        placeholder: 'Tìm sản phẩm…',
        dropdownDirection: 'bottom'
      });
    });
  </script>
@endpush
