    {{-- NAVBAR --}}
    <nav class="lp-nav" id="lpNav">
        <a href="{{ route('login') }}" class="nav-brand">
            <div class="nav-brand-logo">
                <div class="logo-bg">
                    <i class="fa-solid fa-mug-saucer"></i>
                </div>
                <div class="logo-dot"></div>
            </div>
            <div class="nav-brand-text">
                <strong>D'LARIS</strong>
                <small>Cafe &bull; Karaoke</small>
            </div>
        </a>
        <div class="nav-links">
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk
                </a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-plus"></i> Daftar
                </a>
            @endif
        </div>
    </nav>
