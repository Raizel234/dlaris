<section class="hero" id="beranda">
    <div class="hero-pattern"></div>
    <div class="container position-relative" style="z-index:1;">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-badge reveal">
                    <div class="badge-dot"></div>
                    Cafe & Karaoke Premium
                </div>
                <h1 class="hero-title reveal" style="transition-delay:0.1s">
                    Tempat Nongkrong<br>
                    <span class="gold-text">Favorit</span> <span class="outline-text">Kamu</span>
                </h1>
                <p class="hero-desc reveal" style="transition-delay:0.2s">
                    Nikmati kopi spesial, makanan lezat, dan ruang karaoke premium dalam satu tempat. 
                    D'LARIS — tempat di mana setiap momen terasa istimewa.
                </p>
                <div class="d-flex flex-wrap gap-3 reveal" style="transition-delay:0.3s">
                    <a href="{{ route('pelanggan.menu') }}" class="btn-hero-primary">
                        <i class="fa-solid fa-utensils"></i> Lihat Menu
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="btn-hero-outline">
                        <i class="fa-regular fa-calendar-check"></i> Daftar Gratis
                    </a>
                    @endguest
                    @auth
                    <a href="{{ route('pelanggan.booking') }}" class="btn-hero-outline">
                        <i class="fa-solid fa-microphone"></i> Booking Karaoke
                    </a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-image-wrapper reveal-right" style="transition-delay:0.2s">
                    <div class="hero-float-card hero-float-card-1">
                        <div class="card-icon" style="background:rgba(212,160,74,0.12);">
                            <i class="fa-solid fa-star" style="color:var(--gold);font-size:1.2rem;"></i>
                        </div>
                        <div>
                            <div class="card-val">4.9</div>
                            <div class="card-lbl">Rating Pelanggan</div>
                        </div>
                    </div>
                    <div class="hero-float-card hero-float-card-2">
                        <div class="card-icon" style="background:rgba(212,160,74,0.12);">
                            <i class="fa-solid fa-users" style="color:var(--coffee-light);font-size:1.2rem;"></i>
                        </div>
                        <div>
                            <div class="card-val">500+</div>
                            <div class="card-lbl">Pelanggan Puas</div>
                        </div>
                    </div>
                    <div class="hero-image-frame">
                        <img src="{{ asset('images/dlaris.png') }}" alt="D'LARIS Cafe Interior" loading="eager">
                        <div class="img-label">
                            <div class="label-line"></div>
                            <span style="font-size:0.8rem;font-weight:500;font-family:'Inter',sans-serif;letter-spacing:0.5px;">Since 2024</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="scroll-indicator">
        <span>Scroll</span>
        <div class="scroll-line"></div>
    </div>
    <div class="hero-decoration"></div>
</section>
