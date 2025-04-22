@extends('layouts.app')

@section('title','Dashboard')

@section('content')
  <div class="container py-5">
    <h1>Dashboard</h1>
    <p>Chào mừng, {{ Auth::user()->name }}!</p>
  </div>
@endsection
