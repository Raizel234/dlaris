@extends('admin.layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    /* ── RESPONSIVE FIXES ── */
    @media (max-width: 640px) {
        .stat-card .card-value { font-size: 1.35rem; }
        .stat-card { padding: 1rem; }
        .data-table th, .data-table td { padding: 0.6rem 0.75rem; }
        .chart-card { padding: 1rem; }
        .chart-card h3 { font-size: 0.88rem; }
        .rank-item { gap: 0.6rem; padding: 0.6rem 0; }
    }

    /* ── STAT CARDS ── */
    .stat-card {
        border-radius: 20px; padding: 1.5rem;
        position: relative; overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 60px rgba(0,0,0,0.12); }
    .stat-card::before {
        content: '';
        position: absolute; top: -30px; right: -30px;
        width: 100px; height: 100px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
    }
    .stat-card::after {
        content: '';
        position: absolute; bottom: -20px; right: 10px;
        width: 70px; height: 70px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .stat-card .card-icon {
        width: 48px; height: 48px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; color: #fff;
        flex-shrink: 0;
    }
    .stat-card .card-label { font-size: 0.78rem; color: rgba(255,255,255,0.75); font-weight: 500; margin-bottom: 0.25rem; }
    .stat-card .card-value { font-size: 1.75rem; font-weight: 800; color: #fff; line-height: 1.1; letter-spacing: -0.5px; }
    .stat-card .card-change {
        font-size: 0.72rem; margin-top: 0.5rem; font-weight: 600;
        display: flex; align-items: center; gap: 0.3rem;
        color: rgba(255,255,255,0.8);
    }
    .stat-card .card-change .up { color: #a7f3d0; }
    .stat-card .card-change .down { color: #fca5a5; }

    .stat-green  { background: linear-gradient(135deg, #059669, #10b981); }
    .stat-blue   { background: linear-gradient(135deg, #2563eb, #3b82f6); }
    .stat-purple { background: linear-gradient(135deg, #7c3aed, #a855f7); }
    .stat-orange { background: linear-gradient(135deg, #ea580c, #f97316); }
    .stat-teal   { background: linear-gradient(135deg, #0e7490, #06b6d4); }
    .stat-rose   { background: linear-gradient(135deg, #be185d, #ec4899); }

    /* ── CHART SECTION ── */
    .chart-card {
        background: #fff; border-radius: 20px;
        border: 1px solid #f1f5f9;
        padding: 1.5rem;
        transition: box-shadow 0.3s;
    }
    .chart-card:hover { box-shadow: 0 10px 40px rgba(0,0,0,0.06); }
    .chart-card h3 { font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1.25rem; }

    /* ── TABLE ── */
    .data-card {
        background: #fff; border-radius: 20px;
        border: 1px solid #f1f5f9; overflow: hidden;
    }
    .data-card .data-header {
        padding: 1.1rem 1.5rem; border-bottom: 1px solid #f8fafc;
        display: flex; align-items: center; justify-content: space-between;
    }
    .data-card .data-header h3 { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; }
    .data-table th {
        background: #f8fafc; font-size: 0.75rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;
        padding: 0.75rem 1.25rem;
    }
    .data-table td { padding: 0.85rem 1.25rem; font-size: 0.85rem; color: #374151; border-bottom: 1px solid #f8fafc; }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background: #fafafa; }

    /* ── TOP MENU RANK ── */
    .rank-item {
        display: flex; align-items: center; gap: 1rem;
        padding: 0.85rem 0; border-bottom: 1px solid #f8fafc;
    }
    .rank-item:last-child { border-bottom: none; }
    .rank-badge {
        width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.78rem; font-weight: 800;
    }
    .rank-1 { background: #fef9c3; color: #a16207; }
    .rank-2 { background: #f1f5f9; color: #64748b; }
    .rank-3 { background: #fff7ed; color: #c2410c; }
    .rank-other { background: #f8fafc; color: #94a3b8; }
    .rank-bar-wrap { flex: 1; }
    .rank-bar-bg { height: 6px; background: #f1f5f9; border-radius: 50px; overflow: hidden; }
    .rank-bar { height: 100%; border-radius: 50px; background: linear-gradient(90deg, #059669, #10b981); transition: width 1s ease; }

    /* ── MEJA STATUS ── */
    .meja-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

    /* ── LOADING SKELETON ── */
    .skeleton { background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 8px; }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

    /* Animate in */
    .fade-in { animation: fadeIn 0.5s ease forwards; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    .fade-in-delay-1 { animation-delay: 0.1s; opacity: 0; }
    .fade-in-delay-2 { animation-delay: 0.2s; opacity: 0; }
    .fade-in-delay-3 { animation-delay: 0.3s; opacity: 0; }
    .fade-in-delay-4 { animation-delay: 0.4s; opacity: 0; }
</style>
@endpush

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-gray-900" style="letter-spacing:-0.5px;">Selamat datang kembali, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h2>
    <p class="text-gray-500 text-sm mt-0.5">{{ now()->isoFormat('dddd, D MMMM Y') }} — Berikut ringkasan bisnis hari ini.</p>
</div>

{{-- ── STAT CARDS ── --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6" id="statsGrid">
    {{-- Skeleton --}}
    @for($i=0;$i<5;$i++)
    <div class="skeleton h-32 rounded-2xl"></div>
    @endfor
</div>

{{-- ── CHARTS ROW ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
    <div class="lg:col-span-2 chart-card fade-in fade-in-delay-1">
        <div class="flex items-center justify-between mb-4">
            <h3 class="m-0">📈 Pendapatan 7 Hari Terakhir</h3>
            <span class="text-xs text-gray-400 bg-gray-50 px-3 py-1 rounded-full">per hari</span>
        </div>
        <canvas id="chartPendapatan" height="240"></canvas>
    </div>
    <div class="chart-card fade-in fade-in-delay-2">
        <h3>🏆 Top 5 Menu Terlaris</h3>
        <div id="topMenusContainer">
            <div class="skeleton h-10 mb-3 rounded-xl"></div>
            <div class="skeleton h-10 mb-3 rounded-xl"></div>
            <div class="skeleton h-10 mb-3 rounded-xl"></div>
            <div class="skeleton h-10 mb-3 rounded-xl"></div>
            <div class="skeleton h-10 rounded-xl"></div>
        </div>
    </div>
</div>

{{-- ── BOTTOM ROW ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    {{-- Transaksi Terbaru --}}
    <div class="data-card fade-in fade-in-delay-3">
        <div class="data-header">
            <h3>🕐 Transaksi Terbaru</h3>
            <a href="{{ route('admin.transaksi.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold no-underline">Lihat semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full data-table" id="recentTransTable">
                <thead>
                    <tr>
                        <th class="text-left">No. Order</th>
                        <th class="text-left">Waktu</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody id="recentTransBody">
                    <tr><td colspan="3" class="text-center py-6 text-gray-400">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Status Meja + Quick Stats --}}
    <div class="space-y-5 fade-in fade-in-delay-4">
        {{-- Donut Chart Kategori --}}
        <div class="chart-card">
            <h3>🍽️ Penjualan per Kategori</h3>
            <canvas id="chartKategori" height="180"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>if (typeof Chart === 'undefined') { document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"><\/script>'); }</script>
<script>
const rupiah = v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v || 0);
const fmt    = n => new Intl.NumberFormat('id-ID').format(n || 0);

// ── Gradient helper
function makeGradient(ctx, colorTop, colorBottom) {
    const g = ctx.createLinearGradient(0, 0, 0, 300);
    g.addColorStop(0, colorTop);
    g.addColorStop(1, colorBottom);
    return g;
}

// ── LOAD STATS ──────────────────────────────────────────────
function loadStats() {
    fetch('{{ route("admin.dashboard.stats") }}')
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            const data = d.data;

            const cards = [
                {
                    label: 'Pendapatan Hari Ini',
                    value: rupiah(data.revenue_today),
                    icon: 'fa-money-bill-wave',
                    class: 'stat-green',
                    change: data.revenue_change ?? null,
                    changeLabel: 'vs kemarin'
                },
                {
                    label: 'Transaksi Hari Ini',
                    value: fmt(data.transaction_count),
                    icon: 'fa-receipt',
                    class: 'stat-blue',
                    change: data.transaction_change ?? null,
                    changeLabel: 'vs kemarin'
                },
                {
                    label: 'Ruangan Aktif',
                    value: fmt(data.active_rooms),
                    icon: 'fa-microphone',
                    class: 'stat-purple',
                    changeLabel: 'ruangan sedang digunakan'
                },
                {
                    label: 'Pesanan Menunggu',
                    value: fmt(data.pending_orders),
                    icon: 'fa-clock',
                    class: 'stat-orange',
                    changeLabel: 'perlu segera diproses'
                },
                {
                    label: 'Stok Bahan Menipis',
                    value: fmt(data.low_stock_count),
                    icon: 'fa-exclamation-triangle',
                    class: 'stat-rose',
                    changeLabel: data.low_stock_count > 0 ? 'perlu segera diisi' : 'semua aman'
                },
            ];

            document.getElementById('statsGrid').innerHTML = cards.map((c, i) => `
                <div class="stat-card ${c.class} fade-in" style="animation-delay:${i*0.1}s">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:0.75rem;">
                        <div class="card-icon"><i class="fa-solid ${c.icon}"></i></div>
                    </div>
                    <div class="card-label">${c.label}</div>
                    <div class="card-value" id="stat-${i}">${c.value}</div>
                    <div class="card-change">
                        ${c.change !== null && c.change !== undefined
                            ? `<span class="${c.change >= 0 ? 'up' : 'down'}"><i class="fa-solid fa-arrow-${c.change >= 0 ? 'up' : 'down'}"></i> ${Math.abs(c.change)}%</span>`
                            : ''
                        }
                        <span>${c.changeLabel}</span>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => {});
}

// ── LOAD CHART ──────────────────────────────────────────────
let chartPendapatanInst = null;
function loadChart() {
    fetch('{{ route("admin.dashboard.chart") }}')
        .then(r => r.json())
        .then(d => {
            if (!d.success || !d.data.length) return;
            const ctx = document.getElementById('chartPendapatan').getContext('2d');
            if (chartPendapatanInst) chartPendapatanInst.destroy();

            chartPendapatanInst = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: d.data.map(i => i.tanggal),
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: d.data.map(i => i.total),
                            borderColor: '#059669',
                            backgroundColor: makeGradient(ctx, 'rgba(5,150,105,0.2)', 'rgba(5,150,105,0)'),
                            fill: true,
                            tension: 0.45,
                            pointBackgroundColor: '#059669',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                        },
                        {
                            label: 'Pengeluaran',
                            data: d.data.map(i => i.pengeluaran),
                            borderColor: '#ef4444',
                            backgroundColor: makeGradient(ctx, 'rgba(239,68,68,0.15)', 'rgba(239,68,68,0)'),
                            fill: true,
                            tension: 0.45,
                            pointBackgroundColor: '#ef4444',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            borderDash: [5, 5],
                        },
                        {
                            label: 'Laba Bersih',
                            data: d.data.map(i => i.laba),
                            borderColor: '#3b82f6',
                            backgroundColor: 'transparent',
                            tension: 0.45,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { size: 11 },
                                boxWidth: 15,
                                padding: 12,
                                usePointStyle: true,
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 12,
                            cornerRadius: 12,
                            callbacks: {
                                label: ctx => ' ' + ctx.dataset.label + ': ' + rupiah(ctx.raw)
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#94a3b8' } },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                            ticks: {
                                font: { size: 11 }, color: '#94a3b8',
                                callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : (v/1000).toFixed(0)+'rb')
                            }
                        }
                    }
                }
            });
        })
        .catch(() => {});
}

// ── LOAD TOP MENUS ──────────────────────────────────────────
let chartKategoriInst = null;
function loadTopMenus() {
    fetch('{{ route("admin.dashboard.top-menus") }}')
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            const container = document.getElementById('topMenusContainer');
            if (d.data.length) {
                const max = Math.max(...d.data.map(m => m.total_terjual));
                container.innerHTML = d.data.map((m, i) => {
                    const rankClass = i === 0 ? 'rank-1' : i === 1 ? 'rank-2' : i === 2 ? 'rank-3' : 'rank-other';
                    const pct = max > 0 ? Math.round((m.total_terjual / max) * 100) : 0;
                    return `
                    <div class="rank-item">
                        <div class="rank-badge ${rankClass}">${i+1}</div>
                        <div class="rank-bar-wrap">
                            <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                                <span style="font-size:0.82rem;font-weight:600;color:#0f172a;">${m.nama}</span>
                                <span style="font-size:0.75rem;color:#64748b;">${m.total_terjual} terjual</span>
                            </div>
                            <div class="rank-bar-bg">
                                <div class="rank-bar" style="width:0%" data-width="${pct}%"></div>
                            </div>
                        </div>
                    </div>`;
                }).join('');

                // Animate bars
                setTimeout(() => {
                    document.querySelectorAll('.rank-bar').forEach(bar => {
                        bar.style.width = bar.getAttribute('data-width');
                    });
                }, 200);

                // Kategori donut chart (simulated from top menus)
                const ctx = document.getElementById('chartKategori').getContext('2d');
                if (chartKategoriInst) chartKategoriInst.destroy();
                chartKategoriInst = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: d.data.map(m => m.nama),
                        datasets: [{
                            data: d.data.map(m => m.total_terjual),
                            backgroundColor: ['#059669','#3b82f6','#8b5cf6','#f59e0b','#ef4444'],
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverOffset: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '65%',
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 12, font: { size: 11 } } },
                            tooltip: {
                                backgroundColor: '#0f172a', cornerRadius: 10, padding: 10,
                                callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} terjual` }
                            }
                        }
                    }
                });
            } else {
                container.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Belum ada data penjualan</p>';
            }
        })
        .catch(() => {});
}

// ── RECENT TRANSACTIONS ─────────────────────────────────────
function loadRecentTransactions() {
    const params = new URLSearchParams({ per_page: 6 });
    const today = new Date().toISOString().split('T')[0];
    params.set('tanggal', today);
    fetch('{{ route("admin.transaksi.index") }}?' + params.toString(), {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        const tbody = document.getElementById('recentTransBody');
        const rows = d.data || [];
        if (rows.length) {
            tbody.innerHTML = rows.map(t => `
                <tr>
                    <td><span class="font-semibold text-gray-800">${t.kode_transaksi || t.nomor_order || t.id}</span></td>
                    <td class="text-gray-500">${t.created_at ? new Date(t.created_at).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}) : '-'}</td>
                    <td class="text-right font-semibold text-emerald-600">${rupiah(t.total)}</td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-6 text-gray-400 text-sm">Belum ada transaksi hari ini</td></tr>';
        }
    })
    .catch(() => {
        document.getElementById('recentTransBody').innerHTML =
            '<tr><td colspan="3" class="text-center py-6 text-gray-400 text-sm">Tidak dapat memuat data</td></tr>';
    });
}

// ── INIT ────────────────────────────────────────────────────
loadStats();
loadChart();
loadTopMenus();
loadRecentTransactions();

// Auto-refresh stats every 30s
setInterval(() => {
    loadStats();
    loadRecentTransactions();
}, 30000);
</script>
@endpush
