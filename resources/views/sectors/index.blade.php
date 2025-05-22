@extends('layouts.app')

@section('content')
<div class="container py-5">
  <h2 class="mb-4">Ngành hàng</h2>
  <div class="row g-4">
    @foreach($sectors as $s)
      <div class="col-6 col-md-4 col-lg-3 text-center">
        <a href="{{ route('collections.show',$s->collection->slug) }}" class="text-decoration-none text-dark">
          <div class="card border-0 shadow-sm">
            <img src="{{ asset('storage/'.$s->image) }}"
                 class="card-img-top" alt="{{ $s->name }}">
            <div class="card-body p-2">
              <h5 class="card-title mb-0">{{ $s->name }}</h5>
            </div>
          </div>
        </a>
      </div>
    @endforeach
  </div>
</div>
@endsection
