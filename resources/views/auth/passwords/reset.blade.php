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

    .password-reset-card .btn-primary {
        background-color: #4ab3af;
        border-color: #4ab3af;
    }

    .password-reset-card .btn-primary:hover,
    .password-reset-card .btn-primary:focus {
        background-color: #3aa19d;
        border-color: #3aa19d;
    }
</style>
<div class="container" style="padding: 3rem;">
    <div class="row justify-content-center">
        <div class="col-md-8" style="width: 40% !important;">
            <div class="card password-reset-card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body" style="padding: 2rem 0 0 0 !important;">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">Email</label>
                            <div class="col-md-6">
                                <div class="form-control-plaintext">{{ $email ?? old('email') }}</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Mật khẩu') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Nhập lại mật khẩu') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="">
                                <button type="submit" class="btn btn-primary w-100" style="border-radius: 0 0 4px 4px !important;">
                                    {{ __('Reset Password') }}
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
