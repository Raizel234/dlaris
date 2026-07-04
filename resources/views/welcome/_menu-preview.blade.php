<section class="menu-preview-section py-5" id="menu" style="padding-top:5rem!important;padding-bottom:5rem!important;">
    <div class="container py-4">
        <div class="text-center mb-5 reveal">
            <div class="section-badge"><i class="fa-solid fa-utensils"></i> Menu Favorit</div>
            <h2 class="section-title">Paling <span class="gold">Diminati</span></h2>
            <p class="section-sub">Pesan langsung dari meja Anda tanpa harus antri. Cukup scan QR code di meja!</p>
        </div>
        <div class="row g-3" id="menuPopulerContainer">
            <div class="col-6 col-md-3"> <div class="mc-skeleton" style="height:280px;"></div> </div>
            <div class="col-6 col-md-3"> <div class="mc-skeleton" style="height:280px;"></div> </div>
            <div class="col-6 col-md-3 d-none d-md-block"> <div class="mc-skeleton" style="height:280px;"></div> </div>
            <div class="col-6 col-md-3 d-none d-md-block"> <div class="mc-skeleton" style="height:280px;"></div> </div>
        </div>
        <div class="text-center mt-4 reveal">
            <a href="{{ route('pelanggan.menu') }}" class="btn-hero-outline">
                Lihat Menu Lengkap <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>
