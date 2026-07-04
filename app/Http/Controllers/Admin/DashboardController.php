<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pengeluaran;
use App\Models\Ruangan;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function stats()
    {
        $cacheKey = 'dashboard_stats_' . date('Ymd_H');
        $data = Cache::remember($cacheKey, 120, function () {
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();

            $revenueToday = Transaksi::whereDate('created_at', $today)
                ->where('status', 'lunas')->sum('total');

            $transactionCount = Transaksi::whereDate('created_at', $today)
                ->where('status', 'lunas')->count();

            $revenueYesterday = Transaksi::whereDate('created_at', $yesterday)
                ->where('status', 'lunas')->sum('total');

            $transactionYesterday = Transaksi::whereDate('created_at', $yesterday)
                ->where('status', 'lunas')->count();

            $revenueChange = $revenueYesterday > 0
                ? round((($revenueToday - $revenueYesterday) / $revenueYesterday) * 100, 1)
                : null;

            $transactionChange = $transactionYesterday > 0
                ? round((($transactionCount - $transactionYesterday) / $transactionYesterday) * 100, 1)
                : null;

            $activeRooms   = Ruangan::where('status', 'digunakan')->count();
            $pendingOrders = Order::where('status', 'menunggu')->count();
            $lowStockCount = Bahan::whereColumn('stok', '<=', 'stok_minimum')->count();

            return [
                'revenue_today'      => (float) $revenueToday,
                'transaction_count'  => $transactionCount,
                'active_rooms'       => $activeRooms,
                'pending_orders'     => $pendingOrders,
                'low_stock_count'    => $lowStockCount,
                'revenue_change'     => $revenueChange,
                'transaction_change' => $transactionChange,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function chartData()
    {
        $cacheKey = 'dashboard_chart_' . date('Ymd');
        $chartData = Cache::remember($cacheKey, 300, function () {
            $labels = [];
            $income = [];
            $expense = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->isoFormat('DD MMM');

                $totalIncome = Transaksi::whereDate('created_at', $date)
                    ->where('status', 'lunas')->sum('total');

                $totalExpense = Pengeluaran::whereDate('tanggal', $date)->sum('jumlah');

                $income[] = (float) $totalIncome;
                $expense[] = (float) $totalExpense;
            }

            $result = [];
            for ($i = 0; $i < 7; $i++) {
                $result[] = [
                    'tanggal' => $labels[$i],
                    'total' => $income[$i],
                    'pengeluaran' => $expense[$i],
                    'laba' => $income[$i] - $expense[$i],
                ];
            }
            return $result;
        });

        return response()->json([
            'success' => true,
            'data' => $chartData,
        ]);
    }

    public function topMenus()
    {
        $topMenus = Cache::remember('dashboard_top_menus', 300, function () {
            return Menu::select('menus.id', 'menus.nama', 'menus.foto', 'menus.harga', 'kategoris.nama as kategori')
                ->join('kategoris', 'menus.kategori_id', '=', 'kategoris.id')
                ->join('order_items', 'menus.id', '=', 'order_items.menu_id')
                ->selectRaw('SUM(order_items.jumlah) as total_terjual')
                ->selectRaw('SUM(order_items.jumlah * order_items.harga) as total_pendapatan')
                ->groupBy('menus.id', 'menus.nama', 'menus.foto', 'menus.harga', 'kategoris.nama')
                ->orderByDesc('total_terjual')
                ->limit(5)
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => $topMenus,
        ]);
    }
}
