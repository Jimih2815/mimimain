@extends('layouts.admin')

@section('content')
<div class="container-fluid trang-sua-san-pham">
  <h1 class="mb-4">Quản lý Sản phẩm</h1>

  {{-- Thông báo --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- Nút thêm + thanh tìm kiếm/sắp xếp --}}
  <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
    <a href="{{ route('admin.products.create') }}"
       class="btn nut-them-san-pham">
      <i class="fa fa-plus"></i> Thêm sản phẩm mới
    </a>

    <form action="{{ route('admin.products.index') }}"
          method="GET"
          class="d-flex align-items-center gap-2 ms-auto">
      <input type="text"
             name="q"
             value="{{ request('q') }}"
             class="form-control form-control-sm"
             placeholder="Tìm theo tên sản phẩm..."
             style="width:220px;"
             aria-label="Tìm tên sản phẩm">

      <select name="sort" class="form-select form-select-sm" style="width:110px;">
        <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>
          Mới đến cũ
        </option>
        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>
          Cũ đến mới
        </option>
      </select>

      <button type="submit" class="btn btn-outline-secondary btn-sm nut-xanh" style="padding: 0 0.5rem 0 0.5rem; border-radius: 10px !important;">
        Lọc
      </button>
    </form>
  </div>

  {{-- Bảng danh sách --}}
  <table class="table table-bordered table-hover align-middle">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Ảnh</th>
        <th>Tên</th>
        <th>Slug</th>
        <th>Giá gốc</th>
        <th>Options</th>
        <th>Thao tác</th>
      </tr>
    </thead>
    <tbody>
      @forelse($products as $product)
      <tr>
        <td>{{ $product->id }}</td>
        <td>
          @if($product->img)
            <img src="{{ asset('storage/'.$product->img) }}"
                 width="60" height="60"
                 style="object-fit:cover;">
          @else
            —
          @endif

          {{-- thumbnail phụ --}}
          <div class="mt-1 d-flex gap-1">
            @foreach($product->sub_img ?? [] as $sub)
              <img src="{{ asset('storage/'.$sub) }}"
                   width="30" height="30"
                   style="object-fit:cover;border:1px solid #ccc;">
            @endforeach
          </div>
        </td>
        <td>{{ $product->name }}</td>
        <td>{{ $product->slug }}</td>
        <td>{{ number_format($product->base_price,0,',','.') }}₫</td>
        <td>{{ $product->optionValues->count() }}</td>
        <td class="text-center align-middle nut-sua-xoa">
          <div class="d-grid td-action-flex">
            <a href="{{ route('admin.products.edit', $product) }}"
              class="btn nut-sua">Sửa</a>
            <form action="{{ route('admin.products.destroy', $product) }}"
                  method="POST"
                  onsubmit="return confirm('Bạn có chắc muốn xóa?');"
                  style="margin: 0;">
              @csrf @method('DELETE')
              <button class="btn nut-xoa">Xóa</button>
            </form>
          </div>
        </td>

      </tr>
      @empty
      <tr>
        <td colspan="7" class="text-center">Chưa có sản phẩm nào.</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Phân trang custom --}}
@php
    $last    = $products->lastPage();
    $current = $products->currentPage();

    if ($last <= 1) {
        $pages = [1];
    } elseif ($current <= 2) {
        // đầu: 1,2 và cuối: last-1,last
        $pages = [1, 2, $last - 1, $last];
    } elseif ($current >= $last - 1) {
        // cuối: 1,2 và last-1,last
        $pages = [1, 2, $last - 1, $last];
    } else {
        // giữa: 1, prev, current, next, last
        $pages = [1, $current - 1, $current, $current + 1, $last];
    }

    // loại bỏ trùng lặp (ví dụ last-1 có thể trùng 2)
    $pages = array_values(array_unique($pages));
@endphp

<nav aria-label="Trang sản phẩm">
  <ul class="pagination justify-content-center">
    @foreach($pages as $page)
      <li class="page-item {{ $page == $current ? 'active' : '' }}">
        <a class="page-link" href="{{ $products->url($page) }}">{{ $page }}</a>
      </li>
    @endforeach
  </ul>
</nav>
</div>
@endsection
