{{-- resources/views/admin/product-sliders/create.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="trang-slider-san-pham">
  <a href="{{ route('admin.product-sliders.index') }}" class=" nut-quay-ve mb-3">
    <i class="fa-solid fa-chevron-left"></i> Quay về danh sách slider
    </a>
  <h1 class="mb-4" style="color: #b83232; font-size: 3rem;">Thêm Sản Phẩm</h1>

  <form action="{{ route('admin.product-sliders.store') }}"
        method="POST"
        enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
      <label class="form-label">Chọn Sản Phẩm</label>
      <select id="product-select"
              name="product_id"
              class="form-control select-searchable"
              required>
        <option value="">-- chọn sản phẩm --</option>
        @foreach($products as $p)
          <option value="{{ $p->id }}"
            {{ old('product_id') == $p->id ? 'selected':'' }}>
            {{ $p->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Ảnh Slider (nếu bạn muốn override ảnh mặc định của sản phẩm)</label>
      <input type="file"
             name="image"
             class="form-control"
             accept="image/*">
      <small class="form-text text-muted">
        Nếu không upload, hệ thống sẽ dùng `img` đang lưu trong bảng `products`.
      </small>
    </div>

    <div class="mb-3">
      <label class="form-label">Thứ tự (sort order)</label>
      <input type="number"
             name="sort_order"
             class="form-control"
             value="{{ old('sort_order', 0) }}"
             min="0">
    </div>

    <button class="btn-mimi nut-sua">Lưu</button>
    <a href="{{ route('admin.product-sliders.index') }}"
       class="btn-mimi nut-xoa">Hủy</a>
  </form>
</div>
@endsection

@push('styles')
  <!-- TomSelect CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
  <style>
    /* 1) Giấu wrapper thừa, chỉ giữ vùng chứa để dropdown bung ra */
    .ts-wrapper.form-control.select-searchable {
      display: block !important;       /* vẫn là block để dropdown position:absolute */
      border: none !important;
      background: transparent !important;
      padding: 0 !important;
    }

    /* 2) Định style khung chính lên ts-control */
    .ts-control.form-control {
      display: block !important;
      width: 100%;
      border: 1px solid #ced4da !important;
      border-radius: .25rem !important;
      padding: .375rem .75rem !important;
      background-color: #fff !important;
    }

    /* 3) Cho dropdown bung ra ngay bên dưới */
    .ts-dropdown {
      position: absolute !important;
      top: 100%           !important;
      left: 0             !important;
      width: 100%         !important;
      z-index: 1100       !important;
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
