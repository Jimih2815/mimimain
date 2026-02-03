@extends('layouts.app') {{-- layout sẵn có của bạn --}}

@section('content')
<style>
    @media (max-width: 767px) {
        main {
        padding-top: 4rem;
        }
        .mc-floating-panel {
        display: none !important;
        }
    }
</style>
<div class="trang-dang-ky-cont">
    <div class="max-w-md mx-auto mt-10 trang-dang-ky">
        <h1 class="text-2xl font-bold mb-6">Đăng ký</h1>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ url('register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1">Tên</label>
                <input name="name" value="{{ old('name') }}" required
                    class="w-full border rounded p-2" />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div>
                <label class="block mb-1">Số điện thoại</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required
                    class="w-full border rounded p-2" placeholder="0912345678" />
                <x-input-error :messages="$errors->get('phone')" />
            </div>
            <small class="text-muted d-block mb-2">Email không bắt buộc, bạn có thể thêm sau khi đăng nhập.</small>

            <div>
                <label class="block mb-1">Mật khẩu</label>
                <input type="password" name="password" required
                    class="w-full border rounded p-2" />
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div>
                <label class="block mb-1">Nhập lại mật khẩu</label>
                <input type="password" name="password_confirmation" required
                    class="w-full border rounded p-2" />
            </div>

            <button class="w-full bg-amber-500 text-white py-2 rounded">
                Đăng ký
            </button>
        </form>
    </div>
</div>
@endsection
