<!-- MOBILE MENU OVERLAY -->
<div class="mobile-menu-overlay" id="mobileOverlay"></div>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobileMenu">
    <div class="d-flex flex-column gap-1">
        <a href="{{ route('takeaway') }}" class="nav-link-custom" onclick="closeMobileMenu()"><i class="fa-solid fa-bag-shopping me-2"></i>Pesan Takeaway</a>
        <a href="{{ route('pelanggan.menu') }}" class="nav-link-custom" onclick="closeMobileMenu()"><i class="fa-solid fa-utensils me-2"></i>Lihat Menu</a>
        @auth
            @if(in_array(Auth::user()->role, ['super_admin','admin','kasir']))
                <a href="{{ route('admin.dashboard') }}" class="nav-link-custom" onclick="closeMobileMenu()"><i class="fa-solid fa-gauge-high me-2"></i>Dashboard</a>
            @endif
        @endauth
        <hr style="border-color:rgba(74,44,42,0.1);margin:1rem 0;">
        @auth
            @if(in_array(Auth::user()->role, ['super_admin','admin','kasir']))
                <a href="{{ route('admin.dashboard') }}" class="btn-nav-primary justify-content-center">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
            @else
                <a href="{{ route('pelanggan.menu') }}" class="btn-nav-primary justify-content-center">
                    <i class="fa-solid fa-utensils"></i> Pesan Menu
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="d-inline mt-2">
                @csrf
                <button type="submit" class="btn-nav-outline w-100 justify-content-center">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn-nav-outline justify-content-center">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </a>
            <a href="{{ route('register') }}" class="btn-nav-primary justify-content-center mt-2">
                <i class="fa-solid fa-user-plus"></i> Daftar Sekarang
            </a>
        @endauth
    </div>
</div>
