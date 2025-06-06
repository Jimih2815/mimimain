@extends('layouts.app')

@section('content')
<style>
  .help-form-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 2rem;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: #fff;
  }
  .help-form-container h2 {
    margin-bottom: 1.5rem;
    text-align: center;
  }
  .help-form-container .form-group {
    margin-bottom: 1.25rem;
  }
  .help-form-container .form-control {
    width: 100%;
    padding: .5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  .help-form-container .btn-submit {
    border-radius: 100px;
    display: block;
    width: 100%;
    padding: 0.5rem 0.75rem;
    background-color: #d1a029;
    border: 1px solid #d1a029;
    color: #fff;
    font-size: 1.5rem;
    cursor: pointer;
    transition: background-color .2s ease;
    font-weight: 500;
  }
  .help-form-container .btn-submit:hover {
    background-color:rgb(231, 195, 110);
  }
    @media (max-width: 767px) {
    main {
      padding-top: 4rem;
    }
 .mc-floating-panel {
  display: none !important;
}
}
</style>

<div class="help-form-container">
  <h2>Yêu cầu trợ giúp</h2>

  @if(session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif

  <form action="{{ route('help.store') }}" method="POST">
    @csrf

    <div class="form-group">
      <label for="name">Họ và tên</label>
      <input
        type="text"
        id="name"
        name="name"
        class="form-control"
        value="{{ old('name', auth()->user()->name) }}"
        required>
    </div>

    <div class="form-group">
      <label for="phone">Số điện thoại</label>
      <input
        type="text"
        id="phone"
        name="phone"
        class="form-control @error('phone') is-invalid @enderror"
        value="{{ old('phone', auth()->user()->phone ?? '') }}"
        required
        inputmode="numeric"               {{-- hiện bàn phím số trên mobile --}}
        pattern="\d*"                     {{-- chỉ khớp chuỗi toàn số --}}
        oninput="this.value = this.value.replace(/\D/g, '')"  {{-- gạt hết ký tự không phải số --}}
        maxlength="15"                    {{-- giới hạn tối đa, tuỳ bạn chỉnh --}}
      >
      @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>


    <div class="form-group">
      <label for="message">Nội dung yêu cầu</label>
      <textarea
        id="message"
        name="message"
        rows="5"
        class="form-control"
        placeholder="Mô tả vấn đề hoặc yêu cầu hỗ trợ"
        required>{{ old('message') }}</textarea>
    </div>

    <button type="submit" class="btn-submit">Gửi yêu cầu</button>
  </form>
</div>
@endsection
