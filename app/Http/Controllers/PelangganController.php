<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Meja;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ruangan;
use App\Models\Booking;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PelangganController extends Controller
{
    public function menu($nomorMeja = null)
    {
        $meja = null;
        if ($nomorMeja) {
            $meja = Meja::where('nomor_meja', $nomorMeja)->first();
        }

        $kategoris = Kategori::with('menusAktif')->where('is_active', true)->get();

        return view('pelanggan.menu', compact('kategoris', 'meja', 'nomorMeja'));
    }

    public function getMenuByKategori($kategoriId)
    {
        $menus = Menu::where('kategori_id', $kategoriId)
            ->where('is_tersedia', true)
            ->orderBy('is_best_seller', 'desc')
            ->orderBy('nama')
            ->get(['id', 'kategori_id', 'nama', 'harga', 'foto', 'stok', 'deskripsi', 'is_best_seller', 'is_new']);

        return response()->json([
            'success' => true,
            'data' => $menus,
        ]);
    }

    public function searchMenu(Request $request)
    {
        $request->validate(['q' => 'required|string|max:100']);

        $menus = Menu::where('is_tersedia', true)
            ->where('nama', 'like', '%' . $request->q . '%')
            ->orWhereHas('kategori', fn($q) => $q->where('nama', 'like', '%' . $request->q . '%'))
            ->with('kategori:id,nama')
            ->limit(20)
            ->orderBy('is_best_seller', 'desc')
            ->orderBy('nama')
            ->get(['id', 'kategori_id', 'nama', 'harga', 'foto', 'stok', 'deskripsi', 'is_best_seller', 'is_new']);

        return response()->json([
            'success' => true,
            'data' => $menus,
        ]);
    }

    public function menuPopuler()
    {
        $menus = Menu::where('is_tersedia', true)
            ->orderBy('is_best_seller', 'desc')
            ->orderBy('nama')
            ->with('kategori:id,nama')
            ->limit(8)
            ->get(['id', 'nama', 'harga', 'foto', 'stok', 'deskripsi', 'is_best_seller', 'is_new', 'kategori_id']);

        $menus->each(function ($m) {
            $m->foto_url = $m->foto ? asset('storage/' . $m->foto) : null;
        });

        return response()->json([
            'success' => true,
            'data' => $menus,
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:255',
        ]);

        $menu = Menu::findOrFail($request->menu_id);

        if (!$menu->is_tersedia) {
            return response()->json(['success' => false, 'message' => 'Menu tidak tersedia'], 422);
        }

        $cart = session('cart_pelanggan', []);

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

        session(['cart_pelanggan' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Menu ditambahkan ke keranjang',
            'data' => $this->getCartData(),
        ]);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|integer',
            'jumlah' => 'required|integer|min:0',
            'catatan' => 'nullable|string|max:255',
        ]);

        $cart = session('cart_pelanggan', []);

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

        session(['cart_pelanggan' => $cart]);

        return response()->json([
            'success' => true,
            'data' => $this->getCartData(),
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|integer',
            'catatan' => 'nullable|string|max:255',
        ]);

        $cart = session('cart_pelanggan', []);
        $cart = array_values(array_filter($cart, fn($item) =>
            !($item['menu_id'] == $request->menu_id && ($item['catatan'] ?? '') === ($request->catatan ?? ''))
        ));

        session(['cart_pelanggan' => $cart]);

        return response()->json([
            'success' => true,
            'data' => $this->getCartData(),
        ]);
    }

    public function getCart()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getCartData(),
        ]);
    }

    public function clearCart()
    {
        session()->forget('cart_pelanggan');

        return response()->json([
            'success' => true,
            'data' => $this->getCartData(),
        ]);
    }

    public function submitOrder(Request $request)
    {
        $request->validate([
            'meja_id' => 'nullable|integer|exists:mejas,id',
            'catatan' => 'nullable|string|max:500',
        ]);

        $cart = session('cart_pelanggan', []);

        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong'], 422);
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

            session()->forget('cart_pelanggan');

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => $order->load(['items.menu', 'meja']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function riwayat()
    {
        $orders = Order::with(['items.menu', 'meja', 'transaksi'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('pelanggan.riwayat', compact('orders'));
    }

    public function bookingKaraoke()
    {
        $ruangans = Ruangan::where('status', 'tersedia')->get();

        return view('pelanggan.booking', compact('ruangans'));
    }

    public function storeBooking(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'durasi' => 'required|integer|min:1|max:12',
            'catatan' => 'nullable|string|max:500',
        ]);

        $ruangan = Ruangan::findOrFail($request->ruangan_id);
        $user = Auth::user();

        $jamMulai = $request->jam_mulai;
        $jamSelesai = date('H:i', strtotime($jamMulai . ' + ' . $request->durasi . ' hours'));
        $totalHarga = $ruangan->tarif_per_jam * $request->durasi;

        $overlapping = Booking::where('ruangan_id', $request->ruangan_id)
            ->where('tanggal', $request->tanggal)
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->where(fn($q) => $q->where('jam_mulai', '<', $jamSelesai)->where('jam_selesai', '>', $jamMulai))
            ->exists();

        if ($overlapping) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan sudah dibooking pada waktu tersebut',
            ], 409);
        }

        try {
            DB::beginTransaction();

            $booking = Booking::create([
                'user_id' => $user->id,
                'ruangan_id' => $request->ruangan_id,
                'nama_pemesan' => $user->name,
                'nomor_hp' => $user->nomor_hp,
                'tanggal' => $request->tanggal,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'durasi' => $request->durasi,
                'total_harga' => $totalHarga,
                'status' => 'pending',
                'catatan' => $request->catatan,
            ]);

            $booking->load('ruangan');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibuat, menunggu konfirmasi',
                'data' => $booking,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function cekKetersediaan(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'durasi' => 'required|integer|min:1',
        ]);

        $ruangan = Ruangan::findOrFail($request->ruangan_id);
        $jamSelesai = date('H:i', strtotime($request->jam_mulai . ' + ' . $request->durasi . ' hours'));

        $overlapping = Booking::where('ruangan_id', $request->ruangan_id)
            ->where('tanggal', $request->tanggal)
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->where(fn($q) => $q->where('jam_mulai', '<', $jamSelesai)->where('jam_selesai', '>', $request->jam_mulai))
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'tersedia' => !$overlapping,
                'total_harga' => $ruangan->tarif_per_jam * $request->durasi,
            ],
        ]);
    }

    public function daftarBooking()
    {
        $bookings = Booking::with('ruangan')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('pelanggan.booking-list', compact('bookings'));
    }

    private function getCartData(): array
    {
        $cart = session('cart_pelanggan', []);
        $total = array_sum(array_column($cart, 'subtotal'));
        $jumlahItem = array_sum(array_column($cart, 'jumlah'));

        return [
            'items' => $cart,
            'total' => $total,
            'jumlah_item' => $jumlahItem,
        ];
    }
}
