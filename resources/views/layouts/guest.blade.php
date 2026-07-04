<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta property="og:title" content="@yield('title', config('app.name')) — {{ config('app.name') }}">
    <meta property="og:description" content="Tempat nongkrong favorit dengan kopi spesial, makanan lezat, dan karaoke premium. Pesan menu favorit Anda langsung dari meja!">
    <meta property="og:image" content="{{ asset('images/dlaris.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    @vite(['resources/css/bootstrap.css', 'resources/css/guest.css', 'resources/js/bootstrap.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('styles')
</head>
<body>

    @include('layouts.partials._guest-nav')

    @include('layouts.partials._guest-hero')

    @include('layouts.partials._guest-footer')

    @stack('scripts')
    <script>
        const nav = document.getElementById('lpNav');
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    nav.classList.toggle('scrolled', window.scrollY > 30);
                    ticking = false;
                });
                ticking = true;
            }
        });

        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.closest('.form-input-wrap').querySelector('input');
                if (input) {
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    this.innerHTML = isPassword
                        ? '<i class="fa-regular fa-eye-slash"></i>'
                        : '<i class="fa-regular fa-eye"></i>';
                }
            });
        });
    </script>
</body>
</html>