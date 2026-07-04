<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan {{ ucfirst($jenis) }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        .total-row { font-weight: bold; background: #e5e7eb; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $nama_toko ?? "D'LARIS Cafe & Karaoke" }}</h1>
        <p>Laporan {{ ucfirst($jenis) }}</p>
        <p>Periode: {{ $dari }} - {{ $sampai }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Total Transaksi</th>
                <th>Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row->tanggal ?? $row->bulan ?? '-' }}</td>
                <td>{{ $row->total_transaksi ?? 0 }}</td>
                <td>Rp {{ number_format($row->total_pendapatan ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Total</td>
                <td>{{ $rows->sum('total_transaksi') }}</td>
                <td>Rp {{ number_format($rows->sum('total_pendapatan'), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
