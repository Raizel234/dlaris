    {{-- HERO --}}
    <section class="lp-hero">
        <div class="deco deco-1"></div>
        <div class="deco deco-2"></div>
        <div class="deco deco-3"></div>
        <div class="deco deco-4"></div>

        <div class="lp-hero-inner">
            {{-- LEFT: Landing Content (hidden on mobile) --}}
            <div class="hero-left d-none d-md-block">
                <div class="hero-brand-mark">
                    <div class="hm-icon">
                        <i class="fa-solid fa-mug-saucer"></i>
                    </div>
                    <div class="hm-text">
                        <strong>D'LARIS</strong>
                        <small>Cafe &amp; Karaoke</small>
                    </div>
                </div>

                <h1 class="hero-tagline">
                    @yield('hero_title', 'Selamat Datang di <span>D\'LARIS</span>')
                </h1>
                <p class="hero-desc">@yield('hero_sub', 'Nikmati pengalaman bersantap dan bernyanyi terbaik di Kota. Pesan menu favorit Anda langsung dari meja!')</p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="stat-num"><i class="fa-solid fa-utensils"></i> 50+</div>
                        <div class="stat-label">Menu Premium</div>
                    </div>
                    <div class="hero-stat">
                        <div class="stat-num"><i class="fa-solid fa-microphone"></i> 5</div>
                        <div class="stat-label">Ruangan</div>
                    </div>
                    <div class="hero-stat">
                        <div class="stat-num"><i class="fa-solid fa-star"></i> 4.9</div>
                        <div class="stat-label">Rating</div>
                    </div>
                </div>
                <div class="hero-features">
                    <div class="hero-feature">
                        <div class="hf-icon"><i class="fa-solid fa-check"></i></div>
                        Pesan makanan &amp; minuman dari QR meja
                    </div>
                    <div class="hero-feature">
                        <div class="hf-icon"><i class="fa-solid fa-check"></i></div>
                        Booking ruangan karaoke premium
                    </div>
                    <div class="hero-feature">
                        <div class="hf-icon"><i class="fa-solid fa-check"></i></div>
                        Banyak promo &amp; diskon spesial
                    </div>
                </div>
            </div>

            {{-- RIGHT: Form Card --}}
            <div class="hero-right">
                <div class="form-card">
                    @yield('content')
                </div>
            </div>
        </div>
    </section>
