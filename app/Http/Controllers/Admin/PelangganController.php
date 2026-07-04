<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        if (request()->ajax() || request()->wantsJson()) {
            $pelanggans = User::where('role', 'pelanggan')
                ->with('pelanggan')
                ->latest()
                ->paginate(10);

            $items = collect($pelanggans->items())->map(function ($user) {
                $p = $user->pelanggan;
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'no_hp' => $p->nomor_hp ?? $user->nomor_hp,
                    'member_tier' => $user->member_tier ?? 'regular',
                    'poin' => $user->poin ?? 0,
                    'total_kunjungan' => $p->total_kunjungan ?? 0,
                    'total_belanja' => $p->total_belanja ?? 0,
                    'terakhir_kunjungan' => $p->terakhir_kunjungan ?? null,
                    'created_at' => $user->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $items,
                'total' => $pelanggans->total(),
                'per_page' => $pelanggans->perPage(),
                'current_page' => $pelanggans->currentPage(),
                'last_page' => $pelanggans->lastPage(),
            ]);
        }
        return view('admin.pelanggan.index');
    }

    public function show($id)
    {
        $user = User::with('pelanggan')->findOrFail($id);
        $transaksis = Transaksi::whereHas('order', fn($q) => $q->where('user_id', $id))
            ->latest()->take(20)->get();
        $totalTransaksi = Transaksi::whereHas('order', fn($q) => $q->where('user_id', $id))
            ->where('status', 'lunas')->count();
        $totalBelanja = Transaksi::whereHas('order', fn($q) => $q->where('user_id', $id))
            ->where('status', 'lunas')->sum('total');

        return view('admin.pelanggan.show', compact('user', 'transaksis', 'totalTransaksi', 'totalBelanja'));
    }
}
