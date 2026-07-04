<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Cafe & Karaoke Premium</title>
    <meta name="description" content="D'LARIS Cafe & Karaoke — Tempat nongkrong favorit dengan menu kopi spesial, makanan lezat, dan ruang karaoke premium di kota Anda.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta property="og:title" content="{{ config('app.name') }} — Cafe & Karaoke Premium">
    <meta property="og:description" content="Tempat nongkrong favorit dengan kopi spesial, makanan lezat, dan karaoke premium.">
    <meta property="og:image" content="{{ asset('images/dlaris.png') }}">
    <meta property="og:type" content="website">
    @vite(['resources/css/bootstrap.css', 'resources/css/welcome.css', 'resources/js/welcome.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    @include('welcome._mobile-menu')
    @include('welcome._navbar')

    {{-- Desktop: full hero --}}
    <div class="d-none d-md-block">
        @include('welcome._hero')
    </div>

    {{-- Mobile: simple CTA --}}
    <div class="d-md-none hero-mobile d-flex flex-column align-items-center justify-content-center text-center px-4"
         style="min-height:100vh;background:var(--cream);padding-top:70px;">
        <div class="mb-4">
            <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                 style="width:80px;height:80px;background:linear-gradient(135deg,var(--coffee),var(--coffee-light));">
                <i class="fa-solid fa-mug-hot" style="font-size:2rem;color:var(--gold-light);"></i>
            </div>
            <h1 style="font-family:'Playfair Display',serif;font-weight:900;font-size:1.8rem;color:var(--coffee);line-height:1.2;">
                D'<span style="color:var(--gold);">LARIS</span>
            </h1>
            <p style="color:var(--coffee-light);font-size:0.9rem;margin-top:0.5rem;max-width:300px;">
                Cafe & Karaoke Premium — Pesan dari meja Anda
            </p>
        </div>
        <a href="{{ route('pelanggan.menu') }}" class="btn-hero-primary mb-4" style="width:100%;max-width:320px;justify-content:center;padding:1rem;font-size:1.1rem;">
            <i class="fa-solid fa-utensils"></i> Lihat Menu
        </a>
        @auth
            <a href="{{ route('pelanggan.booking') }}" class="btn-hero-outline" style="width:100%;max-width:320px;justify-content:center;padding:0.9rem;">
                <i class="fa-solid fa-microphone"></i> Booking Karaoke
            </a>
        @endauth
        @guest
            <a href="{{ route('register') }}" class="btn-hero-outline" style="width:100%;max-width:320px;justify-content:center;padding:0.9rem;">
                <i class="fa-regular fa-calendar-check"></i> Daftar Gratis
            </a>
            <a href="{{ route('login') }}" class="mt-3" style="color:var(--coffee-light);font-size:0.85rem;">
                Sudah punya akun? <strong style="color:var(--coffee);">Masuk</strong>
            </a>
        @endguest
    </div>

    {{-- Desktop sections (hidden on mobile) --}}
    <div class="d-none d-md-block">
        @include('welcome._stats')
        @include('welcome._services')
        @include('welcome._menu-preview')
        @include('welcome._about')
        @include('welcome._gallery')
        @include('welcome._testimonials')
        @include('welcome._promo')
        @include('welcome._location')
    </div>

    @include('welcome._footer')

    <!-- WhatsApp FAB -->
    <a href="https://wa.me/62817393375" target="_blank" class="whatsapp-fab" title="Chat WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <input type="hidden" id="populerRoute" value="{{ route('pelanggan.menu.populer') }}">
    <input type="hidden" id="placeholderImg" value="{{ asset('images/placeholder.svg') }}">

</body>
</html>
