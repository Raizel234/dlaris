@extends('admin.layouts.app')
@section('title', 'Laporan')

@push('styles')
<style>
    @media (max-width: 640px) {
        #summaryCards > div { padding: 1rem !important; }
        #summaryCards > div p.text-2xl { font-size: 1.25rem; }
    }
</style>
@endpush

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Laporan</h2>
    <p class="text-gray-600">Lihat dan export laporan keuangan</p>
</div>

<div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">Filter Laporan</h3>
    <form id="filterForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" id="dari_tanggal" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" id="sampai_tanggal" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Laporan</label>
            <select id="jenisLaporan" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="harian">Harian</option>
                <option value="bulanan">Bulanan</option>
                <option value="tahunan">Tahunan</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Bayar</label>
            <select id="metodeBayar" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua</option>
                <option value="tunai">Tunai</option>
                <option value="qris">QRIS</option>
                <option value="kartu">Kartu</option>
                <option value="transfer">Transfer</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="button" onclick="tampilkanLaporan()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2"><i class="fa-solid fa-search"></i> Tampilkan</button>
            <button type="button" onclick="exportPdf()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm flex items-center gap-1" title="Export PDF"><i class="fa-solid fa-file-pdf"></i></button>
            <button type="button" onclick="exportExcel()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm flex items-center gap-1" title="Export Excel"><i class="fa-solid fa-file-excel"></i></button>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6" id="summaryCards">
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Total Transaksi</p>
        <p class="text-2xl font-bold text-gray-800" id="totalTransaksi">0</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
        <p class="text-sm text-gray-500">Total Pendapatan</p>
        <p class="text-2xl font-bold text-green-600" id="totalPendapatan">Rp 0</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
        <p class="text-sm text-gray-500">Rata-rata Transaksi</p>
        <p class="text-2xl font-bold text-gray-800" id="rataTransaksi">Rp 0</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-orange-500">
        <p class="text-sm text-gray-500">Menu Terlaris</p>
        <p class="text-2xl font-bold text-gray-800" id="menuTerlaris">-</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">Grafik Pendapatan vs Pengeluaran</h3>
            <span class="text-xs text-gray-400 bg-gray-50 px-3 py-1 rounded-full">laba bersih</span>
        </div>
        <canvas id="chartLaporan" height="250"></canvas>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Pendapatan per Kategori</h3>
        <canvas id="chartKategori" height="250"></canvas>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800">Detail Laporan</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-semibold text-gray-600">
                    <th class="px-6 py-3">Periode</th>
                    <th class="px-6 py-3">Total Transaksi</th>
                    <th class="px-6 py-3">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody id="laporanTableBody" class="divide-y divide-gray-100 text-sm">
                <tr>
                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">Pilih filter dan klik Tampilkan</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
let chartLaporan = null;
let chartKategori = null;

function tampilkanLaporan() {
    const jenis = document.getElementById('jenisLaporan').value;
    const dari = document.getElementById('dari_tanggal').value;
    const sampai = document.getElementById('sampai_tanggal').value;

    const params = new URLSearchParams({ jenis, metode: document.getElementById('metodeBayar').value });

    let url;
    if (jenis === 'harian') {
        params.set('dari', dari || new Date().toISOString().split('T')[0]);
        url = `{{ route('admin.laporan.harian') }}?${params.toString()}`;
    } else if (jenis === 'bulanan') {
        const tgl = dari ? new Date(dari + 'T00:00:00') : new Date();
        params.set('bulan', tgl.getMonth() + 1);
        params.set('tahun', tgl.getFullYear());
        params.set('dari', dari);
        params.set('sampai', sampai);
        url = `{{ route('admin.laporan.bulanan') }}?${params.toString()}`;
    } else {
        const tgl = dari ? new Date(dari + 'T00:00:00') : new Date();
        params.set('tahun', tgl.getFullYear());
        url = `{{ route('admin.laporan.tahunan') }}?${params.toString()}`;
    }

    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            document.getElementById('totalTransaksi').textContent = d.summary?.total_transaksi || 0;
            document.getElementById('totalPendapatan').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(d.summary?.total_pendapatan || 0);
            document.getElementById('rataTransaksi').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(d.summary?.rata_rata || 0);
            document.getElementById('menuTerlaris').textContent = d.summary?.menu_terlaris || '-';

            const tbody = document.getElementById('laporanTableBody');
            if (d.data && d.data.length) {
                tbody.innerHTML = d.data.map(r => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">${r.periode}</td>
                        <td class="px-6 py-4">${r.total_transaksi}</td>
                        <td class="px-6 py-4 font-medium text-green-600">Rp ${new Intl.NumberFormat('id-ID').format(r.total_pendapatan)}</td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">Tidak ada data untuk periode ini</td></tr>';
            }

            if (d.chart) {
                if (chartLaporan) chartLaporan.destroy();
                if (chartKategori) chartKategori.destroy();

                chartLaporan = new Chart(document.getElementById('chartLaporan'), {
                    type: d.chart.tipe || 'bar',
                    data: {
                        labels: d.chart.labels,
                        datasets: [
                            {
                                label: 'Pendapatan',
                                data: d.chart.data,
                                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                borderColor: '#3b82f6',
                                borderWidth: 2
                            },
                            {
                                label: 'Pengeluaran',
                                data: d.chart.pengeluaran || d.chart.data.map(() => 0),
                                backgroundColor: 'rgba(239, 68, 68, 0.3)',
                                borderColor: '#ef4444',
                                borderWidth: 2,
                                borderDash: [5, 5]
                            },
                            {
                                label: 'Laba Bersih',
                                data: d.chart.data.map((v, i) => v - (d.chart.pengeluaran?.[i] || 0)),
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                borderColor: '#22c55e',
                                borderWidth: 2,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, font: { size: 11 } } }
                        },
                        scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp' + v.toLocaleString('id-ID') } } }
                    }
                });

                if (d.chart_kategori) {
                    chartKategori = new Chart(document.getElementById('chartKategori'), {
                        type: 'pie',
                        data: {
                            labels: d.chart_kategori.labels,
                            datasets: [{
                                data: d.chart_kategori.data,
                                backgroundColor: ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899']
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                }
            }
        }
    });
}

function exportPdf() {
    const params = new URLSearchParams({
        dari: document.getElementById('dari_tanggal').value,
        sampai: document.getElementById('sampai_tanggal').value,
        jenis: document.getElementById('jenisLaporan').value,
        metode: document.getElementById('metodeBayar').value,
        bulan: document.getElementById('jenisLaporan').value === 'bulanan' ? new Date((document.getElementById('dari_tanggal').value || new Date().toISOString().split('T')[0]) + 'T00:00:00').getMonth() + 1 : '',
        tahun: ['bulanan', 'tahunan'].includes(document.getElementById('jenisLaporan').value) ? new Date((document.getElementById('dari_tanggal').value || new Date().toISOString().split('T')[0]) + 'T00:00:00').getFullYear() : ''
    });
    window.open(`{{ route('admin.laporan.export-pdf') }}?${params.toString()}`, '_blank');
}

function exportExcel() {
    const params = new URLSearchParams({
        dari: document.getElementById('dari_tanggal').value,
        sampai: document.getElementById('sampai_tanggal').value,
        jenis: document.getElementById('jenisLaporan').value,
        metode: document.getElementById('metodeBayar').value,
        bulan: document.getElementById('jenisLaporan').value === 'bulanan' ? new Date((document.getElementById('dari_tanggal').value || new Date().toISOString().split('T')[0]) + 'T00:00:00').getMonth() + 1 : '',
        tahun: ['bulanan', 'tahunan'].includes(document.getElementById('jenisLaporan').value) ? new Date((document.getElementById('dari_tanggal').value || new Date().toISOString().split('T')[0]) + 'T00:00:00').getFullYear() : ''
    });
    window.open(`{{ route('admin.laporan.export-excel') }}?${params.toString()}`, '_blank');
}

window.addEventListener('load', function() {
    const today = new Date().toISOString().split('T')[0];
    const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
    document.getElementById('dari_tanggal').value = firstDay;
    document.getElementById('sampai_tanggal').value = today;
    tampilkanLaporan();
});
</script>
@endpush
