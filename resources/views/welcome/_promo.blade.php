<section class="promo-section">
    <div class="promo-pattern"></div>
    <div class="container position-relative" style="z-index:1;">
        <div class="text-center text-white mb-5 reveal">
            <div class="section-badge" style="background:rgba(212,160,74,0.15);border-color:rgba(212,160,74,0.3);color:var(--gold);">
                <i class="fa-solid fa-tags"></i> Promo Spesial
            </div>
            <h2 class="section-title text-white" style="color:#fff!important;">
                Dapatkan <span class="gold">Penawaran Terbaik</span>
            </h2>
            <p class="section-sub" style="color:rgba(255,255,255,0.6)!important;">
                Nikmati berbagai promo dan paket hemat untuk pengalaman yang lebih seru.
            </p>
        </div>
        <div class="row g-4 mb-5">
            <div class="col-md-4 reveal" style="transition-delay:0.1s">
                <div class="promo-card">
                    <div class="promo-icon"><i class="fa-solid fa-gift"></i></div>
                    <h5>Paket Ulang Tahun</h5>
                    <p>Rayakan momen spesial dengan paket spesial birthday dari kami!</p>
                </div>
            </div>
            <div class="col-md-4 reveal" style="transition-delay:0.2s">
                <div class="promo-card">
                    <div class="promo-icon"><i class="fa-solid fa-people-group"></i></div>
                    <h5>Paket Gathering</h5>
                    <p>Cocok untuk kumpul bareng teman atau rekan kantor dengan harga hemat.</p>
                </div>
            </div>
            <div class="col-md-4 reveal" style="transition-delay:0.3s">
                <div class="promo-card">
                    <div class="promo-icon"><i class="fa-solid fa-clock"></i></div>
                    <h5>Happy Hour</h5>
                    <p>Nikmati diskon spesial di jam-jam tertentu. Jangan sampai kelewatan!</p>
                </div>
            </div>
        </div>
        <div class="text-center reveal">
            <h3 style="font-family:'Playfair Display',serif;font-size:clamp(1.4rem,3vw,2rem);color:#fff;font-weight:800;margin-bottom:1.5rem;">
                Siap Untuk Bersenang-senang?
            </h3>
            <p style="color:rgba(255,255,255,0.6);margin-bottom:2rem;max-width:450px;margin-left:auto;margin-right:auto;font-size:0.95rem;">
                Pesan menu favorit atau booking ruangan karaoke sekarang dan ciptakan kenangan indah!
            </p>
            <div class="d-flex justify-content-center flex-wrap gap-3">
                <a href="{{ route('pelanggan.menu') }}" class="btn-promo">
                    <i class="fa-solid fa-utensils"></i> Lihat Menu
                </a>
                @auth
                <a href="{{ route('pelanggan.booking') }}" class="btn-promo-outline">
                    <i class="fa-solid fa-microphone"></i> Booking Karaoke
                </a>
                @else
                <a href="{{ route('register') }}" class="btn-promo-outline">
                    <i class="fa-solid fa-user-plus"></i> Daftar Gratis
                </a>
                @endauth
            </div>
        </div>
    </div>
</section>
