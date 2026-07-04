<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();

        $todayOrders = Order::whereIn('status', ['menunggu', 'diproses'])
            ->whereDate('created_at', $today)->count();

        $activeRooms = Ruangan::where('status', 'digunakan')->count();

        $todayBookings = Booking::whereDate('tanggal', $today)->count();

        $menuCount = Menu::where('is_tersedia', true)->count();

        $myLatestAbsensi = auth()->user()->absensis()
            ->whereDate('tanggal', $today)->latest()->first();

        return view('karyawan.dashboard', compact(
            'todayOrders', 'activeRooms', 'todayBookings', 'menuCount', 'myLatestAbsensi'
        ));
    }

    public function menu(Request $request)
    {
        $kategoris = Kategori::where('is_active', true)->get();

        $query = Menu::with('kategori')->where('is_tersedia', true);

        if ($request->kategori) {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->search) {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        $menus = $query->latest()->paginate(12);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $menus->items(),
                'total' => $menus->total(),
                'per_page' => $menus->perPage(),
                'current_page' => $menus->currentPage(),
                'last_page' => $menus->lastPage(),
            ]);
        }

        return view('karyawan.menu', compact('kategoris', 'menus'));
    }

    public function booking()
    {
        $today = Carbon::today();

        $bookings = Booking::with(['ruangan', 'user'])
            ->whereDate('tanggal', $today)
            ->orderBy('jam_mulai')
            ->get();

        $ruangans = Ruangan::orderBy('nama')->get();

        return view('karyawan.booking', compact('bookings', 'ruangans'));
    }

    public function pesanan()
    {
        $orders = Order::with(['items.menu', 'meja', 'user'])
            ->whereIn('status', ['menunggu', 'diproses'])
            ->latest()
            ->get();

        return view('karyawan.pesanan', compact('orders'));
    }
}
