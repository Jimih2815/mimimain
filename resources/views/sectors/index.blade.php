@extends('layouts.app')

{{-- ▼ BẮT ĐẦU CHÈN SIDEBAR --}}
@section('sidebar')
  @include('components.sidebar')
@endsection
{{-- ▲ KẾT THÚC CHÈN SIDEBAR --}}

@section('content')
<style>
h1 {
    font-weight: 700;
    font-size: 3rem;
    color: #b83232;
}
</style>
<div class="container mt-5 mb-5">
  <h1 class="mb-4">Danh mục</h1>
  <div class="row g-4">
    @foreach($sectors as $s)
      <div class="col-6 col-md-4 col-lg-3 text-center">
        <!-- Link về trang /sector/{slug} -->
        <a href="{{ route('sector.show', $s->slug) }}"
           class="text-decoration-none text-dark">
          <div class="card border-0 shadow-sm">
            <img src="{{ asset('storage/'.$s->image) }}"
                 class="card-img-top"
                 alt="{{ $s->name }}">
            <div class="card-body ps-2 pe-2 pt-3 pb-3 ">
              <h5 class=" text-truncate card-title mb-0">{{ $s->name }}</h5>
            </div>
          </div>
        </a>
      </div>
    @endforeach
  </div>
</div>
@endsection
