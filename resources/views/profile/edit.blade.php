@extends('layouts.app')

@section('title','Hồ sơ của tôi')

@section('content')
<div class="container py-5">
  <h2 class="mb-4">Chỉnh sửa hồ sơ</h2>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PUT')

    {{-- Name --}}
    <div class="mb-3">
      <label class="form-label" for="name">Họ & Tên</label>
      <input id="name" name="name" type="text"
             class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name', $user->name) }}" required>
      @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    {{-- Email --}}
    <div class="mb-3">
      <label class="form-label" for="email">Email</label>
      <input id="email" name="email" type="email"
             class="form-control @error('email') is-invalid @enderror"
             value="{{ old('email', $user->email) }}" required>
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary">
      Cập nhật
    </button>
  </form>
</div>
@endsection
