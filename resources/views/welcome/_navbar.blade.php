<nav class="navbar-main" id="mainNavbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <a href="#beranda" class="navbar-brand-logo">
                <div class="logo-icon"><i class="fa-solid fa-mug-hot"></i></div>
                D'<span>LARIS</span>
            </a>

            <div class="d-none d-lg-flex align-items-center gap-1">
                <a href="#beranda" class="nav-link-custom">Beranda</a>
                <a href="#tentang" class="nav-link-custom">Tentang</a>
                <a href="#layanan" class="nav-link-custom">Layanan</a>
                <a href="#menu" class="nav-link-custom">Menu</a>
                <a href="#testimoni" class="nav-link-custom">Testimoni</a>
                <a href="#lokasi" class="nav-link-custom">Lokasi</a>
            </div>

            <div class="d-none d-lg-flex align-items-center gap-2">
                <a href="{{ route('takeaway') }}" class="btn-nav-primary" style="background:var(--coffee-gold);">
                    <i class="fa-solid fa-bag-shopping"></i> Takeaway
                </a>
                @auth
                    @if(in_array(Auth::user()->role, ['super_admin','admin','kasir']))
                        <a href="{{ route('admin.dashboard') }}" class="btn-nav-primary">
                            <i class="fa-solid fa-gauge-high"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('pelanggan.menu') }}" class="btn-nav-primary">
                            <i class="fa-solid fa-utensils"></i> Pesan Menu
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-nav-outline">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link-custom">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-nav-primary">
                        <i class="fa-solid fa-user-plus"></i> Daftar
                    </a>
                @endauth
            </div>

            <button class="hamburger-btn d-lg-none" id="hamburgerBtn" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>
