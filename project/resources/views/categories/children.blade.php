@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="container py-5">
  <h1 class="mb-4">Danh mục: {{ $category->name }}</h1>
  <p>Chọn phân mục con:</p>

  @if($children->isEmpty())
    <div class="alert alert-info">Không có phân mục con.</div>
  @else
    <div class="list-group">
      @foreach($children as $sub)
        <a href="{{ route('categories.show', $sub->id) }}"
           class="list-group-item list-group-item-action">
          {{ $sub->name }}
        </a>
      @endforeach
    </div>
  @endif

  <p class="mt-3">
    <a href="{{ route('categories.index') }}">&larr; Quay về danh mục chính</a>
  </p>
</div>
@endsection
