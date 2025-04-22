@extends('layouts.app')

@section('title', 'Danh mục sản phẩm')

@section('content')
<div class="container py-5">
  <h1 class="mb-4">Chọn danh mục</h1>

  @if($categories->isEmpty())
    <div class="alert alert-info">Chưa có danh mục nào.</div>
  @else
    <div class="list-group">
      @foreach($categories as $cat)
        <a href="{{ route('categories.show', $cat->id) }}"
           class="list-group-item list-group-item-action">
          {{ $cat->name }}
        </a>
      @endforeach
    </div>
  @endif
</div>
@endsection
