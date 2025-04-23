@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-5">
  <h1 class="mb-4">{{ $product->name }}</h1>
  <p>Chọn màu:</p>

  @if($product->classifications->isEmpty())
    <div class="alert alert-info">Chưa có lựa chọn màu nào.</div>
  @else
    <div class="row g-3">
      @foreach($product->classifications as $variant)
        <div class="col-auto text-center">
          <a href="{{ route('products.show', $variant->id) }}"
             class="text-decoration-none">
            <img
              src="{{ asset('storage/'.$variant->img) }}"
              class="img-thumbnail mb-2"
              style="width:100px; height:100px; object-fit:cover;"
              alt="{{ $variant->name }}"
            >
            <div>{{ $variant->name }}</div>
          </a>
        </div>
      @endforeach
    </div>
  @endif

  <p class="mt-4">
    <a href="{{ url()->previous() }}">&larr; Quay lại</a>
  </p>
</div>
@endsection
