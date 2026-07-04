<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Meja;
use App\Models\Ruangan;
use App\Models\Booking;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaksi;
use App\Models\Karyawan;
use App\Models\Pelanggan;
use App\Models\SplitPayment;
use App\Models\Setting;
use App\Services\StockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Kategori::with('menusAktif')->where('is_active', true)->get();
        $mejaList = Meja::orderBy('nomor_meja')->get();
        $ruangans = Ruangan::whereIn('status', ['tersedia', 'digunakan'])->get();

        return view('kasir.pos.index', compact('kategoris', 'mejaList', 'ruangans'));
    }

    public function menuPage()
    {
        $kategoris = Kategori::with('menusAktif')->where('is_active', true)->get();
        $mejaList = Meja::orderBy('nomor_meja')->get();
        $ruangans = Ruangan::whereIn('status', ['tersedia', 'digunakan'])->get();
        return view('kasir.pos.menu', compact('kategoris', 'mejaList', 'ruangans'));
    }

    public function keranjangPage()
    {
        $kategoris = Kategori::with('menusAktif')->where('is_active', true)->get();
        $mejaList = Meja::orderBy('nomor_meja')->get();
        $ruangans = Ruangan::whereIn('status', ['tersedia', 'digunakan'])->get();
        return view('kasir.pos.keranjang', compact('kategoris', 'mejaList', 'ruangans'));
    }

    public function pesananPage()
    {
        $kategoris = Kategori::with('menusAktif')->where('is_active', true)->get();
        $mejaList = Meja::orderBy('nomor_meja')->get();
        $ruangans = Ruangan::whereIn('status', ['tersedia', 'digunakan'])->get();
        return view('kasir.pos.pesanan', compact('kategoris', 'mejaList', 'ruangans'));
    }

    public function karaokePage()
    {
        $kategoris = Kategori::with('menusAktif')->where('is_active', true)->get();
        $mejaList = Meja::orderBy('nomor_meja')->get();
        $ruangans = Ruangan::whereIn('status', ['tersedia', 'digunakan'])->get();
        return view('kasir.pos.karaoke', compact('kategoris', 'mejaList', 'ruangans'));
    }

    public function getMenuByKategori($kategoriId)
    {
        $menus = Menu::where('kategori_id', $kategoriId)
            ->where('is_tersedia', true)
            ->orderBy('nama')
            ->get(['id', 'nama', 'harga', 'foto', 'stok', 'is_tersedia']);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dimuat',
            'data' => $menus,
        ]);
    }

    public function searchMenu(Request $request)
    {
        $request->validate(['q' => 'required|string|max:100']);

        $menus = Menu::where('is_tersedia', true)
            ->where('nama', 'like', '%' . $request->q . '%')
            ->orWhereHas('kategori', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%');
            })
            ->with('kategori:id,nama')
            ->limit(20)
            ->get(['id', 'kategori_id', 'nama', 'harga', 'foto', 'stok']);

        return response()->json([
            'success' => true,
            'message' => 'Hasil pencarian',
            'data' => $menus,
        ]);
    }

    public function getMeja()
    {
        $mejaList = Meja::withCount(['orders' => function ($q) {
            $q->whereIn('status', ['menunggu', 'diproses']);
        }])->orderBy('nomor_meja')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data meja berhasil dimuat',
            'data' => $mejaList,
        ]);
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|integer|exists:menus,id',
            'jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:255',
        ]);

        $menu = Menu::findOrFail($request->menu_id);

        if (!$menu->is_tersedia) {
            return response()->json([
                'success' => false,
                'message' => 'Menu ' . $menu->nama . ' sedang tidak tersedia',
            ], 422);
        }

        $cart = session('cart', []);

        $found = false;
        foreach ($cart as &$item) {
            if ($item['menu_id'] == $request->menu_id && ($item['catatan'] ?? '') === ($request->catatan ?? '')) {
                $item['jumlah'] += $request->jumlah;
                $item['subtotal'] = $item['jumlah'] * $item['harga'];
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = [
                'menu_id' => $menu->id,
                'nama' => $menu->nama,
                'harga' => (float) $menu->harga,
                'jumlah' => $request->jumlah,
                'catatan' => $request->catatan ?? '',
                'foto' => $menu->foto,
                'subtotal' => $request->jumlah * (float) $menu->harga,
            ];
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan ke keranjang',
            'data' => $this->getCartData(),
        ]);
    }

    public function updateItem(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|integer',
            'jumlah' => 'required|integer|min:0',
            'catatan' => 'nullable|string|max:255',
        ]);

        $cart = session('cart', []);

        if ($request->jumlah < 1) {
            $cart = array_values(array_filter($cart, fn($item) =>
                !($item['menu_id'] == $request->menu_id && ($item['catatan'] ?? '') === ($request->catatan ?? ''))
            ));
        } else {
            foreach ($cart as &$item) {
                if ($item['menu_id'] == $request->menu_id && ($item['catatan'] ?? '') === ($request->catatan ?? '')) {
                    $item['jumlah'] = $request->jumlah;
                    $item['subtotal'] = $item['jumlah'] * $item['harga'];
                    break;
                }
            }
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui',
            'data' => $this->getCartData(),
        ]);
    }

    public function removeItem(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|integer',
            'catatan' => 'nullable|string|max:255',
        ]);

        $cart = session('cart', []);

        $cart = array_values(array_filter($cart, fn($item) =>
            !($item['menu_id'] == $request->menu_id && ($item['catatan'] ?? '') === ($request->catatan ?? ''))
        ));

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang',
            'data' => $this->getCartData(),
        ]);
    }

    public function getCart()
    {
        return response()->json([
            'success' => true,
            'message' => 'Data keranjang berhasil dimuat',
            'data' => $this->getCartData(),
        ]);
    }

    public function clearCart()
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan',
            'data' => $this->getCartData(),
        ]);
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'meja_id' => 'nullable|integer|exists:mejas,id',
            'catatan' => 'nullable|string|max:500',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang masih kosong',
            ], 422);
        }

        $total = array_sum(array_column($cart, 'subtotal'));

        try {
            DB::beginTransaction();

            $nomorOrder = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $serviceChargePersen = (float) \App\Models\Setting::getValue('service_charge', 0);
            $pajakPersen = (float) \App\Models\Setting::getValue('pajak', 0);

            $order = Order::create([
                'user_id' => Auth::id(),
                'meja_id' => $request->meja_id,
                'nomor_order' => $nomorOrder,
                'status' => 'menunggu',
                'catatan' => $request->catatan,
                'total' => $total,
            ]);

            $order->hitungGrandTotal($serviceChargePersen, $pajakPersen)->save();

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['menu_id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'catatan' => $item['catatan'] ?? null,
                ]);

                $menu = Menu::find($item['menu_id']);
                if ($menu && $menu->stok !== null && $menu->stok > 0) {
                    $menu->decrement('stok', $item['jumlah']);
                }
            }

            DB::commit();

            session()->forget('cart');

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => $order->load(['items.menu', 'meja']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getOrders()
    {
        $orders = Order::with(['items.menu', 'meja', 'user'])
            ->where(function ($q) {
                $q->whereIn('status', ['menunggu', 'diproses'])
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'selesai')
                         ->whereDoesntHave('transaksi');
                  });
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pesanan berhasil dimuat',
            'data' => $orders,
        ]);
    }

    public function updateStatusOrder(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai,dibatalkan',
        ]);

        $order = Order::findOrFail($id);

        if ($request->status === 'dibatalkan' && $order->status !== 'dibatalkan') {
            app(StockService::class)->processVoidStock($order);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diubah',
            'data' => $order->load(['items.menu', 'meja']),
        ]);
    }

    public function pembayaran(Request $request, $id)
    {
        $request->validate([
            'metode_bayar' => 'required|in:tunai,transfer,qris',
            'nominal_bayar' => 'required|numeric|min:0',
        ]);

        $order = Order::with('items')->findOrFail($id);

        if ($order->status === 'dibatalkan') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan sudah dibatalkan, tidak bisa diproses',
            ], 422);
        }

        if ($order->transaksi && $order->transaksi->status === 'lunas') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini sudah lunas',
            ], 422);
        }

        $totalBayar = $order->grand_total > 0 ? $order->grand_total : $order->total;
        $kembalian = $request->nominal_bayar - $totalBayar;

        if ($kembalian < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal bayar kurang dari total pembayaran',
            ], 422);
        }

        $kodeTransaksi = 'TRX-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        try {
            DB::beginTransaction();

            $transaksi = Transaksi::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'kode_transaksi' => $kodeTransaksi,
                'metode_bayar' => $request->metode_bayar,
                'total' => $totalBayar,
                'nominal_bayar' => $request->nominal_bayar,
                'kembalian' => $kembalian,
                'status' => 'lunas',
            ]);

            $order->update(['status' => 'selesai']);

            $this->updatePelangganData($order);

            app(StockService::class)->processPaymentStock($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil',
                'data' => $transaksi->load(['order.items.menu', 'order.meja']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Pembayaran gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function formPembayaran($id)
    {
        $order = Order::with(['items.menu', 'meja', 'user'])->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pembayaran berhasil dimuat',
                'data' => $order,
            ]);
        }

        return view('kasir.pos.pembayaran', compact('order'));
    }

    public function hitungKembalian(Request $request)
    {
        $request->validate([
            'total' => 'required|numeric|min:0',
            'nominal_bayar' => 'required|numeric|min:0',
        ]);

        $kembalian = $request->nominal_bayar - $request->total;

        if ($kembalian < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal bayar kurang Rp ' . number_format(abs($kembalian), 0, ',', '.'),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kembalian berhasil dihitung',
            'data' => [
                'total' => (float) $request->total,
                'nominal_bayar' => (float) $request->nominal_bayar,
                'kembalian' => $kembalian,
            ],
        ]);
    }

    public function cetakStruk($id)
    {
        $transaksi = Transaksi::with([
            'user',
            'order.meja',
            'order.items.menu',
            'order.user',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('kasir.pos.struk', compact('transaksi'));

        return $pdf->stream('struk-' . $transaksi->kode_transaksi . '.pdf');
    }

    public function getNotifications()
    {
        $newOrders = Order::with(['items.menu', 'meja'])
            ->where('status', 'menunggu')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->latest()
            ->get();

        $completedOrders = Order::where('status', 'selesai')
            ->where('updated_at', '>=', now()->subMinutes(5))
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dimuat',
            'data' => [
                'new_orders' => $newOrders,
                'total_new' => $newOrders->count(),
                'completed_count' => $completedOrders,
            ],
        ]);
    }

    public function bookingKaraoke(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|integer|exists:ruangans,id',
            'nama_pemesan' => 'required|string|max:100',
            'nomor_hp' => 'required|string|max:20',
            'durasi' => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:500',
        ]);

        $ruangan = Ruangan::findOrFail($request->ruangan_id);

        $jamMulai = now()->format('H:i');
        $jamSelesai = now()->addHours($request->durasi)->format('H:i');
        $totalHarga = $ruangan->tarif_per_jam * $request->durasi;

        try {
            DB::beginTransaction();

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'ruangan_id' => $request->ruangan_id,
                'nama_pemesan' => $request->nama_pemesan,
                'nomor_hp' => $request->nomor_hp,
                'tanggal' => now()->toDateString(),
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'durasi' => $request->durasi,
                'total_harga' => $totalHarga,
                'status' => 'confirmed',
                'catatan' => $request->catatan,
            ]);

            if ($ruangan->status === 'tersedia') {
                $ruangan->update(['status' => 'digunakan']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking karaoke berhasil',
                'data' => $booking->load('ruangan'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Booking gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getActiveBookings()
    {
        $this->autoCompleteExpiredBookings();

        $activeBookings = Booking::with('ruangan')
            ->whereIn('status', ['confirmed', 'ongoing'])
            ->whereDate('tanggal', now()->toDateString())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Booking aktif berhasil dimuat',
            'data' => $activeBookings,
        ]);
    }

    public function startTimer(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if (!in_array($booking->status, ['confirmed', 'ongoing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak dapat dimulai',
            ], 422);
        }

        $booking->update([
            'status' => 'ongoing',
            'jam_mulai' => now()->format('H:i'),
            'jam_selesai' => now()->addHours($booking->durasi)->format('H:i'),
        ]);

        $booking->ruangan->update(['status' => 'digunakan']);

        return response()->json([
            'success' => true,
            'message' => 'Timer sesi karaoke dimulai',
            'data' => $booking->load('ruangan'),
        ]);
    }

    public function extendSession(Request $request, $id)
    {
        $request->validate([
            'tambah_jam' => 'required|integer|min:1|max:6',
        ]);

        $booking = Booking::findOrFail($id);

        if (!in_array($booking->status, ['confirmed', 'ongoing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak aktif',
            ], 422);
        }

        $tambahJam = $request->tambah_jam;
        $jamSelesaiSekarang = \Carbon\Carbon::parse($booking->jam_selesai);
        $jamSelesaiBaru = $jamSelesaiSekarang->copy()->addHours($tambahJam);
        $tambahanHarga = $booking->ruangan->tarif_per_jam * $tambahJam;

        $booking->update([
            'jam_selesai' => $jamSelesaiBaru->format('H:i'),
            'durasi' => $booking->durasi + $tambahJam,
            'total_harga' => $booking->total_harga + $tambahanHarga,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sesi karaoke berhasil diperpanjang ' . $tambahJam . ' jam',
            'data' => $booking->load('ruangan'),
        ]);
    }

    public function getTimerStatus($id)
    {
        $booking = Booking::with('ruangan')->findOrFail($id);

        $jamMulai = \Carbon\Carbon::parse($booking->jam_mulai);
        $jamSelesai = \Carbon\Carbon::parse($booking->jam_selesai);
        $now = now();

        $totalDetik = $jamMulai->diffInSeconds($jamSelesai);
        $detikBerlalu = $jamMulai->diffInSeconds($now, false);
        $sisaDetik = max(0, $totalDetik - $detikBerlalu);

        $progress = $totalDetik > 0 ? min(100, ($detikBerlalu / $totalDetik) * 100) : 0;

        $isExpired = $sisaDetik <= 0;

        return response()->json([
            'success' => true,
            'message' => 'Status timer',
            'data' => [
                'booking' => $booking,
                'total_detik' => $totalDetik,
                'detik_berlalu' => max(0, $detikBerlalu),
                'sisa_detik' => $sisaDetik,
                'progress' => round($progress, 1),
                'is_expired' => $isExpired,
                'jam_mulai' => $booking->jam_mulai,
                'jam_selesai' => $booking->jam_selesai,
            ],
        ]);
    }

    public function splitPembayaran(Request $request, $id)
    {
        $request->validate([
            'payments' => 'required|array|min:2',
            'payments.*.metode_bayar' => 'required|in:tunai,transfer,qris,kartu',
            'payments.*.jumlah' => 'required|numeric|min:0',
            'payments.*.nominal_bayar' => 'required|numeric|min:0',
        ]);

        $order = Order::with('items')->findOrFail($id);

        if ($order->status === 'dibatalkan') {
            return response()->json(['success' => false, 'message' => 'Pesanan sudah dibatalkan'], 422);
        }
        if ($order->transaksi && $order->transaksi->status === 'lunas') {
            return response()->json(['success' => false, 'message' => 'Pesanan ini sudah lunas'], 422);
        }

        $orderTotal = $order->grand_total > 0 ? $order->grand_total : $order->total;
        $totalBayar = array_sum(array_column($request->payments, 'jumlah'));
        if (abs($totalBayar - $orderTotal) > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Total pembayaran split (' . number_format($totalBayar, 0, ',', '.') . ') tidak sesuai dengan total pesanan (' . number_format($orderTotal, 0, ',', '.') . ')',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $kodeTransaksi = 'TRX-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $transaksi = Transaksi::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'kode_transaksi' => $kodeTransaksi,
                'metode_bayar' => 'split',
                'total' => $orderTotal,
                'nominal_bayar' => $totalBayar,
                'kembalian' => 0,
                'status' => 'lunas',
                'is_split' => true,
                'tipe_bayar' => 'split',
            ]);

            foreach ($request->payments as $pay) {
                $kembalian = $pay['nominal_bayar'] - $pay['jumlah'];
                SplitPayment::create([
                    'transaksi_id' => $transaksi->id,
                    'order_id' => $order->id,
                    'metode_bayar' => $pay['metode_bayar'],
                    'jumlah' => $pay['jumlah'],
                    'nominal_bayar' => $pay['nominal_bayar'],
                    'kembalian' => max(0, $kembalian),
                ]);
            }

            $order->update(['status' => 'selesai']);

            $this->updatePelangganData($order);

            app(StockService::class)->processPaymentStock($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran split berhasil',
                'data' => $transaksi->load(['order.items.menu', 'order.meja', 'splitPayments']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function updatePelangganData(Order $order): void
    {
        if ($order->user_id && $order->user->role === 'pelanggan') {
            $pelanggan = Pelanggan::firstOrCreate(
                ['user_id' => $order->user_id],
                ['nomor_hp' => $order->user->nomor_hp]
            );

            $totalBayar = $order->grand_total > 0 ? $order->grand_total : $order->total;

            $pelanggan->increment('total_kunjungan');
            $pelanggan->increment('total_belanja', $totalBayar);
            $pelanggan->update(['terakhir_kunjungan' => now()]);

            if ($pelanggan->user->poin !== null) {
                $pelanggan->user->tambahPoin(floor($totalBayar / 1000));
            }
        }
    }

    private function autoCompleteExpiredBookings(): void
    {
        $expired = Booking::whereIn('status', ['ongoing', 'confirmed'])
            ->where(function ($q) {
                $q->whereDate('tanggal', '<', now()->toDateString())
                  ->orWhere(function ($q2) {
                      $q2->whereDate('tanggal', '=', now()->toDateString())
                         ->whereTime('jam_selesai', '<=', now()->toTimeString());
                  });
            })
            ->get();

        foreach ($expired as $booking) {
            $booking->ruangan->update(['status' => 'tersedia']);
            $booking->update(['status' => 'selesai']);
        }
    }

    private function getCartData(): array
    {
        $cart = session('cart', []);
        $total = array_sum(array_column($cart, 'subtotal'));
        $jumlahItem = array_sum(array_column($cart, 'jumlah'));

        return [
            'items' => $cart,
            'total' => $total,
            'jumlah_item' => $jumlahItem,
        ];
    }
}
