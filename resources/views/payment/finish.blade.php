<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Berhasil — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:system-ui,-apple-system,sans-serif; background:linear-gradient(135deg,#fefce8,#fffbeb); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1rem; }
        .card { background:#fff; border-radius:24px; box-shadow:0 20px 60px rgba(0,0,0,0.1); max-width:420px; width:100%; padding:2.5rem 2rem; text-align:center; }
        .icon { width:72px; height:72px; border-radius:50%; background:#d1fae5; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; }
        .icon i { font-size:2rem; color:#10b981; }
        h1 { font-size:1.5rem; font-weight:800; color:#1f2937; margin-bottom:0.5rem; }
        .sub { color:#6b7280; font-size:0.9rem; margin-bottom:2rem; }
        .info { background:#f9fafb; border-radius:12px; padding:1rem; margin-bottom:1.5rem; text-align:left; }
        .info-row { display:flex; justify-content:space-between; padding:0.35rem 0; font-size:0.85rem; }
        .info-label { color:#6b7280; }
        .info-value { font-weight:600; color:#1f2937; }
        .btn { display:inline-flex; align-items:center; gap:0.5rem; padding:0.75rem 2rem; border-radius:12px; font-weight:600; font-size:0.9rem; border:none; cursor:pointer; text-decoration:none; transition:all 0.2s; }
        .btn-primary { background:#1f2937; color:#fff; }
        .btn-primary:hover { background:#374151; }
        .btn-outline { background:transparent; border:1px solid #d1d5db; color:#374151; }
        .btn-outline:hover { background:#f9fafb; }
        .flex { display:flex; gap:0.75rem; justify-content:center; margin-top:1.5rem; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon"><i class="fa-solid fa-check"></i></div>
        <h1>Pembayaran Berhasil</h1>
        <p class="sub">Terima kasih! Pembayaran Anda telah diterima.</p>

        @if($order)
        <div class="info">
            <div class="info-row"><span class="info-label">No. Order</span><span class="info-value">{{ $order->nomor_order }}</span></div>
            <div class="info-row"><span class="info-label">Total</span><span class="info-value">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span></div>
            <div class="info-row"><span class="info-label">Status</span><span class="info-value" style="color:#10b981;">Lunas</span></div>
        </div>
        @endif

        <p style="font-size:0.8rem;color:#9ca3af;margin-bottom:0.5rem;">Pesanan Anda sedang diproses</p>

        <div class="flex">
            <a href="{{ route('pelanggan.riwayat') }}" class="btn btn-primary"><i class="fa-solid fa-clock-rotate-left"></i> Lihat Pesanan</a>
            <a href="{{ route('beranda') }}" class="btn btn-outline"><i class="fa-solid fa-house"></i> Beranda</a>
        </div>
    </div>
</body>
</html>
