@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1>{{ $collection->name }}</h1>
  @if($collection->description)
    <p>{{ $collection->description }}</p>
  @endif

  <div class="row">
    @foreach($collection->products as $p)
      <div class="col-md-3 mb-4">
        @include('components.product-card',['product'=>$p])
      </div>
    @endforeach
  </div>
</div>
@endsection
