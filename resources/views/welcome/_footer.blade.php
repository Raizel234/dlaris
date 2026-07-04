<footer class="py-5">
    <div class="container">
        <div class="row g-4 pb-4">
            <div class="col-lg-4">
                <div class="footer-brand mb-3">D'<span>LARIS</span></div>
                <p class="mb-3">Cafe & Karaoke terbaik untuk Anda dan keluarga. Nikmati momen tak terlupakan bersama kami.</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="footer-social"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="footer-social"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="footer-social"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="footer-social"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="mb-3">Navigasi</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('pelanggan.menu') }}">Menu</a></li>
                    @auth
                    <li class="mb-2"><a href="{{ route('pelanggan.booking') }}">Booking</a></li>
                    <li class="mb-2"><a href="{{ route('pelanggan.riwayat') }}">Riwayat</a></li>
                    @endauth
                    <li class="mb-2"><a href="#layanan">Layanan</a></li>
                    <li class="mb-2"><a href="#tentang">Tentang</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="mb-3">Jam Operasional</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fa-regular fa-clock me-2" style="color:var(--gold);"></i>Setiap Hari: 10:00–23:00</li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h6 class="mb-3">Kontak</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fa-solid fa-location-dot me-2" style="color:var(--gold);"></i>Jl. Contoh No. 123, Kota</li>
                    <li class="mb-2"><i class="fa-solid fa-phone me-2" style="color:var(--gold);"></i>(021) 1234-56789</li>
                    <li class="mb-2"><i class="fa-solid fa-envelope me-2" style="color:var(--gold);"></i>info@dlaris.com</li>
                </ul>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,0.06);">
        <p class="text-center mb-0" style="font-size:0.8rem;color:rgba(255,255,255,0.3);">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved. Made with <i class="fa-solid fa-heart" style="color:#ef4444;"></i>
        </p>
    </div>
</footer>
