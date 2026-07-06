<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Tertunda — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:system-ui,-apple-system,sans-serif; background:linear-gradient(135deg,#fefce8,#fffbeb); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1rem; }
        .card { background:#fff; border-radius:24px; box-shadow:0 20px 60px rgba(0,0,0,0.1); max-width:420px; width:100%; padding:2.5rem 2rem; text-align:center; }
        .icon { width:72px; height:72px; border-radius:50%; background:#fef3c7; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; }
        .icon i { font-size:2rem; color:#d97706; }
        h1 { font-size:1.5rem; font-weight:800; color:#1f2937; margin-bottom:0.5rem; }
        .sub { color:#6b7280; font-size:0.9rem; margin-bottom:2rem; }
        .btn { display:inline-flex; align-items:center; gap:0.5rem; padding:0.75rem 2rem; border-radius:12px; font-weight:600; font-size:0.9rem; border:none; cursor:pointer; text-decoration:none; transition:all 0.2s; background:#1f2937; color:#fff; }
        .btn:hover { background:#374151; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon"><i class="fa-solid fa-clock"></i></div>
        <h1>Pembayaran Tertunda</h1>
        <p class="sub">Anda belum menyelesaikan pembayaran. Silakan lanjutkan pembayaran melalui halaman pesanan.</p>
        <a href="{{ route('pelanggan.riwayat') }}" class="btn"><i class="fa-solid fa-clock-rotate-left"></i> Ke Pesanan</a>
    </div>
</body>
</html>
