<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) - {{ config('app.name') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta property="og:title" content="@yield('title', config('app.name')) — {{ config('app.name') }}">
    <meta property="og:description" content="Jelajahi menu lengkap D'LARIS Cafe & Karaoke. Nikmati pengalaman bersantap dan bernyanyi terbaik.">
    <meta property="og:image" content="{{ asset('images/dlaris.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    @vite(['resources/css/bootstrap.css', 'resources/js/bootstrap.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @stack('styles')
    <style>
        :root {
            --cream: #faf6f0; --cream-dark: #f0e8dc;
            --coffee: #4a2c2a; --coffee-light: #7a4f4a;
            --gold: #d4a04a; --gold-light: #e8c878;
            --gold-glow: rgba(212, 160, 74, 0.25);
            --dark: #1a1412; --warm-white: #fdfcf9;
            --font-body: 'Inter', system-ui, -apple-system, sans-serif;
            --font-heading: 'Playfair Display', serif;
        }
        body {
            font-family: var(--font-body);
            background: #f8f6f3;
            color: var(--coffee);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--coffee), var(--coffee-light));
            border-color: var(--coffee);
            color: var(--gold-light);
        }
        .btn-primary:hover {
            background: var(--coffee);
            border-color: var(--coffee);
            color: var(--gold-light);
        }
        .btn-outline-primary {
            color: var(--coffee);
            border-color: var(--coffee);
        }
        .btn-outline-primary:hover {
            background: var(--coffee);
            border-color: var(--coffee);
            color: var(--gold-light);
        }
        .text-primary { color: var(--coffee) !important; }
        .bg-primary { background: linear-gradient(135deg, var(--coffee) 0%, var(--coffee-light) 100%) !important; }
        .navbar {
            background: rgba(74, 44, 42, 0.97) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .navbar-brand {
            font-family: var(--font-heading);
            font-weight: 900;
            font-size: 1.25rem;
        }
        .navbar-brand span { color: var(--gold); }
        .navbar .nav-link {
            font-weight: 500;
            transition: all 0.2s;
        }
        .navbar .nav-link:hover { color: var(--gold-light) !important; }
        .navbar .dropdown-menu {
            border-color: rgba(74,44,42,0.06);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .navbar .dropdown-item:hover {
            background: var(--cream);
            color: var(--coffee);
        }
        .page-link {
            color: var(--coffee);
            border-color: rgba(74,44,42,0.1);
        }
        .page-item.active .page-link {
            background: var(--coffee);
            border-color: var(--coffee);
            color: var(--gold-light);
        }
        .page-link:hover {
            color: var(--coffee-light);
            background: var(--cream);
            border-color: rgba(74,44,42,0.1);
        }
        .cart-fab {
            position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 1040;
            width: 56px; height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--coffee), var(--coffee-light));
            color: var(--gold-light); border: none;
            display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
            box-shadow: 0 8px 30px rgba(74,44,42,0.35);
            transition: all 0.3s; cursor: pointer;
        }
        .cart-fab:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 12px 40px rgba(74,44,42,0.45); color: #fff; }
        .cart-fab .badge-keranjang {
            position: absolute; top: -4px; right: -4px;
            background: var(--gold); color: var(--coffee);
            font-size: 0.65rem; font-weight: 800; min-width: 20px; height: 20px;
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff; padding: 0 4px;
        }
        footer small { color: var(--coffee-light); opacity: 0.5; }
        @media (max-width: 576px) {
            .navbar-brand { font-size: 0.95rem; }
            .navbar .nav-link { font-size: 0.82rem; padding: 0.35rem 0; }
            main.py-4 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
            .cart-fab { width: 48px; height: 48px; font-size: 1.1rem; bottom: 1rem; right: 1rem; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('beranda') }}">
                <i class="fa-solid fa-mug-hot me-1" style="color:var(--gold-light);"></i>D'<span>LARIS</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPelanggan">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarPelanggan">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link fw-medium" href="{{ route('pelanggan.menu') }}"><i class="fa-solid fa-utensils me-1"></i>Menu</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link fw-medium" href="{{ route('pelanggan.booking') }}"><i class="fa-solid fa-calendar-check me-1"></i>Booking</a></li>
                        <li class="nav-item"><a class="nav-link fw-medium" href="{{ route('pelanggan.riwayat') }}"><i class="fa-solid fa-clock-rotate-left me-1"></i>Riwayat</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-medium" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-circle-user me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end rounded-3 shadow-sm border-0">
                                <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="fa-solid fa-gear me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger py-2"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link fw-medium" href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket me-1"></i>Login</a></li>
                        <li class="nav-item"><a class="nav-link fw-medium" href="{{ route('register') }}"><i class="fa-solid fa-user-plus me-1"></i>Daftar</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-3">
        @yield('content')
    </main>

    <footer class="bg-white border-top mt-4 py-3 text-center text-muted small">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} D'LARIS Cafe &amp; Karaoke. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
