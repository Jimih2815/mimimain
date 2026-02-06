<nav class="navbar bg-white py-3 mb-4 chamcong-admin-navbar">
    <div class="nav-container">
        <a href="{{ route('admin.dashboard') }}" class="nav-logo-link">
            <img src="{{ asset('image/mimi-logo.webp') }}" alt="Mimi Admin" class="nav-logo">
        </a>

        @if(session('is_admin') === true)
            <div class="nav-logout">
                <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="logout-btn">Đăng xuất</button>
                </form>
            </div>
        @endif
    </div>
</nav>
