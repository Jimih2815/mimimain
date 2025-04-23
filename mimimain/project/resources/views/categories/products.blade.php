@extends('layouts.app')

@section('title', 'Sản phẩm: '.$category->name)

@section('content')
<div class="container py-5">
  <h1 class="mb-4">Sản phẩm trong “{{ $category->name }}”</h1>

  @if($products->isEmpty())
    <div class="alert alert-info">Chưa có sản phẩm nào.</div>
  @else
    <div class="list-group">
      @foreach($products as $product)
        <a href="{{ route('product.show', $product->id) }}"
           class="list-group-item list-group-item-action">
          {{ $product->name }}
        </a>
      @endforeach
    </div>
  @endif

  <p class="mt-3">
    <a href="{{ route('categories.index') }}">&larr; Quay về danh mục chính</a>
  </p>
</div>
@endsection
