{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="py-4 admin-dashboard">
  <h1 class="mb-4">🚀 Admin Dashboard</h1>

  <div class="row g-3 danh-muc-cont mt-4">
    {{-- Home Page --}}
      <div class="col-md-3 danh-sach">
        <a href="{{ route('admin.home.edit') }}" class="card h-100 text-center text-decoration-none">
          <div class="card-body">
            <i class="bi bi-house fs-1"></i>
            <h5 class="mt-2">Trang chủ</h5>
          </div>
        </a>
      </div>


    {{-- Sản phẩm --}}
      <div class="col-md-3 danh-sach">
        <a href="{{ route('admin.products.index') }}" class="card h-100 text-center text-decoration-none">
          <div class="card-body">
            <i class="bi bi-box-seam fs-1"></i>
            <h5 class="mt-2">Sản phẩm</h5>
          </div>
        </a>
      </div>


    {{-- Mega‐menu --}}
    <div class="col-md-3 danh-sach">
      <a href="{{ route('admin.menu.index') }}" class="card h-100 text-center text-decoration-none">
        <div class="card-body">
          <i class="bi bi-menu-button-wide fs-1"></i>
          <h5 class="mt-2">Header</h5>
        </div>
      </a>
    </div>


    {{-- Side bar --}}
    <div class="col-md-3 danh-sach">
      <a href="{{ route('admin.sidebar-items.index') }}" class="card h-100 text-center text-decoration-none">
        <div class="card-body">
          <i class="bi bi-layout-sidebar fs-1"></i>
          <h5 class="mt-2">Side Bar</h5>
        </div>
      </a>
    </div> 

    {{-- Collections --}}
    <div class="col-md-3 danh-sach">
      <a href="{{ route('admin.collections.index') }}" class="card h-100 text-center text-decoration-none">
        <div class="card-body">
          <i class="bi bi-images fs-1"></i>
          <h5 class="mt-2">Collections</h5>
        </div>
      </a>
    </div>

    {{-- Đơn hàng --}}
    <div class="col-md-3 danh-sach">
      <a href="{{ route('admin.orders.index') }}" class="card h-100 text-center text-decoration-none">
        <div class="card-body">
          <i class="bi bi-receipt fs-1"></i>
          <h5 class="mt-2">Đơn hàng</h5>
        </div>
      </a>
    </div>

    {{-- Người dùng --}}
    <div class="col-md-3 danh-sach">
      <a href="{{ route('admin.users.index') }}" class="card h-100 text-center text-decoration-none">
        <div class="card-body">
          <i class="bi bi-people fs-1"></i>
          <h5 class="mt-2">Người dùng</h5>
        </div>
      </a>
    </div>


    {{-- Product Sliders --}}
    <div class="col-md-3 danh-sach">
      <a href="{{ route('admin.product-sliders.index') }}" class="card h-100 text-center text-decoration-none">
        <div class="card-body">
          <i class="bi bi-sliders fs-1"></i>
          <h5 class="mt-2">Product Sliders</h5>
        </div>
      </a>
    </div>

    {{-- Collection Sliders --}}
    <div class="col-md-3 danh-sach">
      <a href="{{ route('admin.collection-sliders.index') }}" class="card h-100 text-center text-decoration-none">
        <div class="card-body">
          <i class="bi bi-easel fs-1"></i>
          <h5 class="mt-2">Collection Sliders</h5>
        </div>
      </a>
    </div>

    

    {{-- Home Section Images --}}
    <div class="col-md-3 danh-sach">
      <a href="{{ route('admin.home-section-images.index') }}" class="card h-100 text-center text-decoration-none">
        <div class="card-body">
          <i class="bi bi-images fs-1"></i>
          <h5 class="mt-2">Home Images</h5>
        </div>
      </a>
    </div> 

    



  <!-- {{-- Widgets --}} -->
    <!-- <div class="col-md-3 danh-sach">
      <a href="{{ route('admin.widgets.index') }}" class="card h-100 text-center text-decoration-none">
        <div class="card-body">
          <i class="bi bi-puzzle fs-1"></i>
          <h5 class="mt-2">Widgets</h5>
        </div>
      </a>
    </div> -->

    <!-- {{-- Widget Placements --}} -->
    <!-- <div class="col-md-3 danh-sach">
      <a href="{{ route('admin.placements.index') }}" class="card h-100 text-center text-decoration-none">
        <div class="card-body">
          <i class="bi bi-pin-angle fs-1"></i>
          <h5 class="mt-2">Widget Placements</h5>
        </div>
      </a>
    </div> -->
  </div>
</div>
@endsection
