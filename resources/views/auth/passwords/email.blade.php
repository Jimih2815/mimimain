@extends('layouts.app')

@section('content')
<style>
    .password-reset-card {
        border: 1px solid #4ab3af;
        border-radius: 5px !important;
    }

    .password-reset-card .card-header {
        background-color: rgba(74, 179, 175, 0.2);
        border-bottom: 1px solid #4ab3af;
        color: #2f7f7c;
        font-weight: 600;
        font-size: 1.1rem;
        text-align: center;
    }

    .password-reset-card .form-control:focus {
        border-color: #4ab3af;
        box-shadow: 0 0 0 0.2rem rgba(74, 179, 175, 0.25);
    }

    .password-reset-card .alert-success {
        background-color: rgba(74, 179, 175, 0.15);
        border-color: #4ab3af;
        color: #2f7f7c;
    }
</style>
<div class="container" style="padding: 3rem;">
    <div class="row justify-content-center">
        <div class="col-md-8" style="width: 40% !important;">
            <div class="card password-reset-card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body" style="padding: 2rem 0 0 0 !important;;">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert" style="margin-right: 4rem; margin-left: 4rem; text-align: center;">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="phone" class="col-md-4 col-form-label text-md-end">Số điện thoại</label>

                            <div class="col-md-6">
                                <input id="phone" type="tel"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       name="phone" value="{{ old('phone') }}" required
                                       placeholder="0912345678" autofocus>

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>

                            <div class="col-md-6">
                                <input id="email" type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}"
                                       placeholder="you@email.com" autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <!-- <small class="text-muted d-block mt-1">
                                    Nếu tài khoản đã có email, bạn không cần nhập ô này.
                                </small> -->
                            </div>
                        </div>

                        <div class="w-100">
                            <div class="w-100">
                                <button type="submit" class="btn btn-primary nut-xanh w-100" style="border-radius: 0 0 5px 5px !important;">
                                    {{ __('Gửi Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
