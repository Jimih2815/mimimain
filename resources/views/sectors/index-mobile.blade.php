{{-- resources/views/sectors/index-mobile.blade.php --}}
@extends('layouts.app-mobile')

@section('content')
<style>
.chu-do {
    font-weight: 700;
    font-size: 2rem;
    color: #b83232;
}
.card-title {
    font-weight: 600;
}
</style>
<div class="container py-4 ">
  <h1 class="h3 mb-4 chu-do">Danh mục</h1>
  <div class="row g-3">
    @foreach($sectors as $s)
      <div class="col-6">
        <a href="{{ route('sector.show', $s->slug) }}" class="text-decoration-none">
          <div class="card">
            <img src="{{ asset('storage/'.$s->image) }}" 
                 class="card-img-top" 
                 alt="{{ $s->name }}">
            <div class="card-body py-3 px-3 text-center" >
              <h6 class="card-title text-truncate mb-0">{{ $s->name }}</h6>
            </div>
          </div>
        </a>
      </div>
    @endforeach
  </div>
</div>
@endsection
