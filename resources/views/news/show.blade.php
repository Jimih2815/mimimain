@extends('layouts.app')

@section('content')
<div class="w-50 mx-auto mt-5 mb-5">
  <h1 class="mb-4">{{ $post->title }}</h1>
  <div class="prose">
    {!! $post->content !!}
  </div>
</div>
@endsection
