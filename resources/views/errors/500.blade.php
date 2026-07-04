<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — D'LARIS</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta property="og:title" content="500 — D'LARIS">
    <meta property="og:description" content="D'LARIS Cafe & Karaoke — Kesalahan Server">
    <meta property="og:image" content="{{ asset('images/dlaris.png') }}">
    <meta property="og:type" content="website">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --coffee: #4a2c2a; --coffee-light: #7a4f4a;
            --gold: #d4a04a; --cream: #faf6f0; --dark: #1a1412;
            --warm-white: #fdfcf9;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Inter',system-ui,sans-serif;
            background: var(--warm-white); color: var(--dark);
            min-height:100vh; display:flex; align-items:center; justify-content:center;
            overflow:hidden;
        }
        .bg-pattern {
            position:fixed; inset:0; opacity:0.03;
            background-image:repeating-linear-gradient(45deg, var(--coffee) 0px, var(--coffee) 1px, transparent 1px, transparent 30px);
        }
        .card {
            position:relative; z-index:1; text-align:center; max-width:480px; padding:2rem;
        }
        .icon {
            font-size:5rem; color:var(--gold); margin-bottom:1rem;
            animation: float 3s ease-in-out infinite;
        }
        h1 { font-family:'Playfair Display',serif; font-size:6rem; font-weight:900; color:var(--coffee); line-height:1; }
        h2 { font-family:'Playfair Display',serif; font-size:1.5rem; color:var(--coffee); margin-bottom:0.75rem; }
        p { color:var(--coffee-light); font-size:0.95rem; line-height:1.7; margin-bottom:2rem; opacity:0.8; }
        .btn {
            display:inline-flex; align-items:center; gap:0.5rem;
            background:linear-gradient(135deg,var(--coffee),var(--coffee-light));
            color:var(--gold-light); border:none; border-radius:50px;
            padding:0.85rem 2rem; font-size:0.95rem; font-weight:700;
            text-decoration:none; transition:all 0.3s; font-family:inherit;
        }
        .btn:hover { color:#fff; transform:translateY(-3px); box-shadow:0 15px 40px rgba(74,44,42,0.3); }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
    </style>
</head>
<body>
    <div class="bg-pattern"></div>
    <div class="card">
        <div class="icon"><i class="fa-solid fa-gear"></i></div>
        <h1>500</h1>
        <h2>Kesalahan Server</h2>
        <p>Maaf, terjadi kesalahan pada server kami. Tim teknis telah diberitahu dan akan segera memperbaikinya.</p>
        <a href="{{ url('/') }}" class="btn"><i class="fa-solid fa-house"></i> Kembali ke Beranda</a>
    </div>
</body>
</html>
