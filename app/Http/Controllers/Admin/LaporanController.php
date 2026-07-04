<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\Pengeluaran;
use App\Models\Transaksi;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        return view('admin.laporan.index');
    }

    public function harian(Request $request)
    {
        $tanggal = $request->dari ? Carbon::parse($request->dari) : Carbon::today();
        $metode = $request->metode;

        $query = Transaksi::with(['user', 'order.meja', 'booking.ruangan'])
            ->whereDate('created_at', $tanggal)
            ->where('status', 'lunas');

        if ($metode) {
            $query->where('metode_bayar', $metode);
        }

        $transaksis = $query->get();

        $totalPendapatan = $transaksis->sum('total');
        $jumlahTransaksi = $transaksis->count();

        $menuTerlaris = OrderItem::select('menu_id', DB::raw('SUM(jumlah) as total'))
            ->whereHas('order', fn($q) => $q->whereDate('created_at', $tanggal))
            ->groupBy('menu_id')
            ->orderByDesc('total')
            ->with('menu')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Laporan harian berhasil dimuat',
            'summary' => [
                'total_transaksi' => $jumlahTransaksi,
                'total_pendapatan' => (float) $totalPendapatan,
                'rata_rata' => $jumlahTransaksi > 0 ? (float) ($totalPendapatan / $jumlahTransaksi) : 0,
                'menu_terlaris' => $menuTerlaris?->menu?->nama ?? '-',
            ],
            'data' => $transaksis->map(fn($t) => [
                'periode' => $t->created_at->format('H:i'),
                'total_transaksi' => 1,
                'total_pendapatan' => (float) $t->total,
                'kode' => $t->kode_transaksi,
                'metode' => $t->metode_bayar,
            ]),
            'chart' => [
                'labels' => ['Pendapatan'],
                'data' => [(float) $totalPendapatan],
                'pengeluaran' => [(float) Pengeluaran::whereDate('tanggal', $tanggal)->sum('jumlah')],
                'tipe' => 'bar',
            ],
        ]);
    }

    public function bulanan(Request $request)
    {
        $bulan = $request->bulan ?? ($request->dari ? Carbon::parse($request->dari)->month : now()->month);
        $tahun = $request->tahun ?? ($request->dari ? Carbon::parse($request->dari)->year : now()->year);
        $metode = $request->metode;

        $query = Transaksi::with(['user', 'order.meja', 'booking.ruangan'])
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'lunas');

        if ($metode) {
            $query->where('metode_bayar', $metode);
        }

        $transaksis = $query->get();

        $totalPendapatan = $transaksis->sum('total');
        $jumlahTransaksi = $transaksis->count();

        $menuTerlaris = OrderItem::select('menu_id', DB::raw('SUM(jumlah) as total'))
            ->whereHas('order', fn($q) => $q->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun))
            ->groupBy('menu_id')
            ->orderByDesc('total')
            ->with('menu')
            ->first();

        $perHari = [];
        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::createFromDate($tahun, $bulan, $i);
            $total = Transaksi::whereDate('created_at', $date)
                ->where('status', 'lunas')
                ->sum('total');
            $expense = Pengeluaran::whereDate('tanggal', $date)->sum('jumlah');
            $perHari[] = [
                'periode' => $date->isoFormat('DD MMM'),
                'total_transaksi' => 1,
                'total_pendapatan' => (float) $total,
                'pengeluaran' => (float) $expense,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan bulanan berhasil dimuat',
            'summary' => [
                'total_transaksi' => $jumlahTransaksi,
                'total_pendapatan' => (float) $totalPendapatan,
                'rata_rata' => $jumlahTransaksi > 0 ? (float) ($totalPendapatan / $jumlahTransaksi) : 0,
                'menu_terlaris' => $menuTerlaris?->menu?->nama ?? '-',
            ],
            'data' => $perHari,
            'chart' => [
                'labels' => array_column($perHari, 'periode'),
                'data' => array_column($perHari, 'total_pendapatan'),
                'pengeluaran' => array_column($perHari, 'pengeluaran'),
                'tipe' => 'line',
            ],
        ]);
    }

    public function tahunan(Request $request)
    {
        $tahun = $request->tahun ?? now()->year;
        $metode = $request->metode;

        $query = Transaksi::with(['user', 'order.meja', 'booking.ruangan'])
            ->whereYear('created_at', $tahun)
            ->where('status', 'lunas');

        if ($metode) {
            $query->where('metode_bayar', $metode);
        }

        $transaksis = $query->get();

        $totalPendapatan = $transaksis->sum('total');
        $jumlahTransaksi = $transaksis->count();

        $menuTerlaris = OrderItem::select('menu_id', DB::raw('SUM(jumlah) as total'))
            ->whereHas('order', fn($q) => $q->whereYear('created_at', $tahun))
            ->groupBy('menu_id')
            ->orderByDesc('total')
            ->with('menu')
            ->first();

        $perBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulan = Carbon::createFromDate($tahun, $i, 1);
            $total = Transaksi::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $i)
                ->where('status', 'lunas')
                ->sum('total');
            $expense = Pengeluaran::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $i)
                ->sum('jumlah');

            $perBulan[] = [
                'periode' => $bulan->isoFormat('MMMM'),
                'total_transaksi' => 1,
                'total_pendapatan' => (float) $total,
                'pengeluaran' => (float) $expense,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan tahunan berhasil dimuat',
            'summary' => [
                'total_transaksi' => $jumlahTransaksi,
                'total_pendapatan' => (float) $totalPendapatan,
                'rata_rata' => $jumlahTransaksi > 0 ? (float) ($totalPendapatan / $jumlahTransaksi) : 0,
                'menu_terlaris' => $menuTerlaris?->menu?->nama ?? '-',
            ],
            'data' => $perBulan,
            'chart' => [
                'labels' => array_column($perBulan, 'periode'),
                'data' => array_column($perBulan, 'total_pendapatan'),
                'pengeluaran' => array_column($perBulan, 'pengeluaran'),
                'tipe' => 'bar',
            ],
        ]);
    }

    public function perKategori(Request $request)
    {
        $dari = $request->dari ? Carbon::parse($request->dari) : Carbon::now()->startOfMonth();
        $sampai = $request->sampai ? Carbon::parse($request->sampai) : Carbon::now();

        $kategoris = Kategori::with(['menus.orderItems' => function ($q) use ($dari, $sampai) {
            $q->whereHas('order', fn($q) => $q->whereBetween('created_at', [$dari, $sampai]));
        }])->get();

        $data = $kategoris->map(function ($kategori) {
            $totalTerjual = 0;
            $totalPendapatan = 0;

            foreach ($kategori->menus as $menu) {
                foreach ($menu->orderItems as $item) {
                    $totalTerjual += $item->jumlah;
                    $totalPendapatan += $item->jumlah * (float) $item->harga;
                }
            }

            return [
                'kategori' => $kategori->nama,
                'total_terjual' => $totalTerjual,
                'total_pendapatan' => $totalPendapatan,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Laporan per kategori berhasil dimuat',
            'data' => [
                'dari' => $dari->isoFormat('DD MMMM YYYY'),
                'sampai' => $sampai->isoFormat('DD MMMM YYYY'),
                'kategoris' => $data,
            ],
        ]);
    }

    public function exportPdf(Request $request)
    {
        $jenis = $request->jenis ?? 'harian';
        $dari = $request->dari;
        $sampai = $request->sampai;
        $rows = collect();

        if ($jenis === 'harian') {
            $tanggal = $dari ? Carbon::parse($dari) : Carbon::today();
            $transaksis = Transaksi::whereDate('created_at', $tanggal)
                ->where('status', 'lunas')->get();
            $rows = $transaksis->map(fn($t) => (object) [
                'tanggal' => $t->created_at->format('d/m/Y'),
                'total_transaksi' => 1,
                'total_pendapatan' => (float) $t->total,
            ]);
            $dari = $sampai = $tanggal->isoFormat('DD MMMM YYYY');
        } elseif ($jenis === 'bulanan') {
            $bulan = $request->bulan ?? now()->month;
            $tahun = $request->tahun ?? now()->year;
            $date = Carbon::createFromDate($tahun, $bulan, 1);
            $transaksis = Transaksi::whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->where('status', 'lunas')->get();
            $rows = $transaksis->map(fn($t) => (object) [
                'tanggal' => $t->created_at->format('d/m/Y'),
                'total_transaksi' => 1,
                'total_pendapatan' => (float) $t->total,
            ]);
            $dari = $date->isoFormat('MMMM YYYY');
            $sampai = '';
        } else {
            $tahun = $request->tahun ?? now()->year;
            $transaksis = Transaksi::whereYear('created_at', $tahun)
                ->where('status', 'lunas')->get();
            $rows = $transaksis->map(fn($t) => (object) [
                'tanggal' => $t->created_at->format('d/m/Y'),
                'total_transaksi' => 1,
                'total_pendapatan' => (float) $t->total,
            ]);
            $dari = 'Tahun ' . $tahun;
            $sampai = '';
        }

        $data = compact('jenis', 'dari', 'sampai', 'rows');

        $pdf = Pdf::loadView('admin.laporan.pdf', $data);

        return $pdf->download('laporan-' . $jenis . '-' . now()->format('YmdHis') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $jenis = $request->jenis ?? 'harian';

        $rows = collect();

        if ($jenis === 'harian') {
            $tanggal = $request->tanggal ? Carbon::parse($request->tanggal) : Carbon::today();
            $rows = Transaksi::whereDate('created_at', $tanggal)
                ->where('status', 'lunas')
                ->get(['kode_transaksi', 'total', 'metode_bayar', 'created_at']);
        } elseif ($jenis === 'bulanan') {
            $bulan = $request->bulan ?? now()->month;
            $tahun = $request->tahun ?? now()->year;
            $rows = Transaksi::whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->where('status', 'lunas')
                ->get(['kode_transaksi', 'total', 'metode_bayar', 'created_at']);
        } else {
            $tahun = $request->tahun ?? now()->year;
            $rows = Transaksi::whereYear('created_at', $tahun)
                ->where('status', 'lunas')
                ->get(['kode_transaksi', 'total', 'metode_bayar', 'created_at']);
        }

        $csv = "Kode Transaksi,Total,Metode Bayar,Tanggal\n";
        foreach ($rows as $r) {
            $csv .= "{$r->kode_transaksi},{$r->total},{$r->metode_bayar},{$r->created_at}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-' . $jenis . '-' . now()->format('YmdHis') . '.csv"',
        ]);
    }
}
