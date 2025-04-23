{{-- resources/views/admin/products/index.blade.php --}}

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Quản lý Sản phẩm</h1>

    {{-- Hiện thông báo thành công --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Nút thêm mới --}}
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">
        <i class="fa fa-plus"></i> Thêm sản phẩm mới
    </a>

    <table class="table table-bordered table-hover">
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
                             alt="{{ $product->name }}" width="60" height="60" style="object-fit:cover;">
                    @else
                        —
                    @endif
                </td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->slug }}</td>
                <td>{{ number_format($product->base_price, 0, ',', '.') }}₫</td>
                <td>{{ $product->optionValues->count() }}</td>
                <td class="d-flex gap-1">
                    <a href="{{ route('admin.products.edit', $product) }}"
                       class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('admin.products.destroy', $product) }}"
                          method="POST"
                          onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Chưa có sản phẩm nào.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>
@endsection
