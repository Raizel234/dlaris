<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk {{ $transaksi->kode_transaksi }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 10px; width: 58mm; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 5px; }
        .header h1 { font-size: 14px; margin: 0; }
        .header p { margin: 2px 0; font-size: 9px; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; }
        td { padding: 1px 0; }
        .right { text-align: right; }
        .total { font-weight: bold; font-size: 12px; }
        .footer { text-align: center; margin-top: 10px; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>{{ \App\Models\Setting::getValue('alamat', '') }}</p>
        <p>Telp: {{ \App\Models\Setting::getValue('telepon', '') }}</p>
    </div>
    <div class="line"></div>
    <p>No: {{ $transaksi->kode_transaksi }}</p>
    <p>Tanggal: {{ $transaksi->created_at->format('d/m/Y H:i') }}</p>
    <p>Kasir: {{ $transaksi->user?->name ?? '-' }}</p>
    @if($transaksi->order)
        @if($transaksi->order->meja)
    <p>Meja: {{ $transaksi->order->meja->nomor_meja }}</p>
        @elseif($transaksi->order->tipe_pesanan)
    <p>Tipe: {{ $transaksi->order->tipe_pesanan == 'takeaway' ? 'Take Away' : 'Delivery' }}</p>
            @if($transaksi->order->nama_pelanggan)
    <p>Pelanggan: {{ $transaksi->order->nama_pelanggan }} @if($transaksi->order->no_hp)({{ $transaksi->order->no_hp }})@endif</p>
            @endif
        @endif
    @endif
    <div class="line"></div>
    <table>
        @if($transaksi->order)
        @foreach($transaksi->order->items as $item)
        <tr>
            <td colspan="2">{{ $item->menu?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>{{ $item->jumlah }} x Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        @endif
        @if($transaksi->booking)
        <tr>
            <td colspan="2">Booking: {{ $transaksi->booking->ruangan?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>{{ $transaksi->booking->durasi }} jam</td>
            <td class="right">Rp {{ number_format($transaksi->booking->total_harga, 0, ',', '.') }}</td>
        </tr>
        @endif
    </table>
    <div class="line"></div>
    <table>
        @if($transaksi->order && $transaksi->order->service_charge > 0)
        <tr>
            <td>Service Charge</td>
            <td class="right">Rp {{ number_format($transaksi->order->service_charge, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($transaksi->order && $transaksi->order->pajak > 0)
        <tr>
            <td>Pajak</td>
            <td class="right">Rp {{ number_format($transaksi->order->pajak, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="total">
            <td>Total</td>
            <td class="right">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar</td>
            <td class="right">Rp {{ number_format($transaksi->nominal_bayar ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td class="right">Rp {{ number_format($transaksi->kembalian ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Metode Bayar</td>
            <td class="right">{{ strtoupper($transaksi->metode_bayar) }}</td>
        </tr>
    </table>
    <div class="line"></div>
    <div class="footer">
        <p>Terima Kasih</p>
        <p>~ D'LARIS Cafe & Karaoke ~</p>
    </div>
</body>
</html>
