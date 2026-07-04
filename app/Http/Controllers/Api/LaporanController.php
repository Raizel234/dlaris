<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function harian(Request $request): JsonResponse
    {
        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal) : Carbon::today();

        $transaksis = Transaksi::with('order.items.menu', 'booking.ruangan', 'user')
            ->whereDate('created_at', $tanggal)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPendapatan = $transaksis->where('status', 'lunas')->sum('total');
        $jumlahTransaksi = $transaksis->count();
        $transaksiLunas = $transaksis->where('status', 'lunas')->count();

        $orders = Order::with('items.menu')
            ->whereDate('created_at', $tanggal)
            ->get();

        $totalOrder = $orders->count();
        $totalItemTerjual = $orders->sum(function ($order) {
            return $order->items->sum('jumlah');
        });

        return response()->json([
            'success' => true,
            'message' => 'Laporan harian berhasil diambil.',
            'data' => [
                'tanggal' => $tanggal->format('Y-m-d'),
                'ringkasan' => [
                    'total_pendapatan' => (float) $totalPendapatan,
                    'jumlah_transaksi' => $jumlahTransaksi,
                    'transaksi_lunas' => $transaksiLunas,
                    'total_order' => $totalOrder,
                    'total_item_terjual' => $totalItemTerjual,
                ],
                'transaksis' => $transaksis,
                'orders' => $orders,
            ],
        ]);
    }

    public function bulanan(Request $request): JsonResponse
    {
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $transaksis = Transaksi::with('order.items.menu', 'booking.ruangan', 'user')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'desc')
            ->get();

        $pendapatanPerHari = $transaksis
            ->where('status', 'lunas')
            ->groupBy(function ($item) {
                return Carbon::parse($item->created_at)->format('Y-m-d');
            })
            ->map(function ($items) {
                return [
                    'tanggal' => $items->first()->created_at->format('Y-m-d'),
                    'total' => (float) $items->sum('total'),
                    'jumlah_transaksi' => $items->count(),
                ];
            })
            ->values();

        $totalPendapatan = $transaksis->where('status', 'lunas')->sum('total');
        $jumlahTransaksi = $transaksis->count();
        $transaksiLunas = $transaksis->where('status', 'lunas')->count();

        $orders = Order::with('items.menu')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        $totalItemTerjual = $orders->sum(function ($order) {
            return $order->items->sum('jumlah');
        });

        return response()->json([
            'success' => true,
            'message' => 'Laporan bulanan berhasil diambil.',
            'data' => [
                'bulan' => (int) $bulan,
                'tahun' => (int) $tahun,
                'ringkasan' => [
                    'total_pendapatan' => (float) $totalPendapatan,
                    'jumlah_transaksi' => $jumlahTransaksi,
                    'transaksi_lunas' => $transaksiLunas,
                    'total_order' => $orders->count(),
                    'total_item_terjual' => $totalItemTerjual,
                ],
                'pendapatan_per_hari' => $pendapatanPerHari,
                'transaksis' => $transaksis,
            ],
        ]);
    }
}
