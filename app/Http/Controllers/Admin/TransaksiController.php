<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VoidTransaksiRequest;
use App\Models\Transaksi;
use App\Services\StockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $perPage = request('per_page', 15);
            $transaksis = Transaksi::with(['user', 'order.meja', 'booking.ruangan'])
                ->when(request('tanggal'), fn($q) => $q->whereDate('created_at', request('tanggal')))
                ->when(request('metode'), fn($q) => $q->where('metode_bayar', request('metode')))
                ->when(request('status'), fn($q) => $q->where('status', request('status')))
                ->latest()
                ->paginate($perPage);
            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil dimuat',
                'data' => $transaksis->items(),
                'total' => $transaksis->total(),
                'per_page' => $transaksis->perPage(),
                'current_page' => $transaksis->currentPage(),
                'last_page' => $transaksis->lastPage(),
            ]);
        }
        return view('admin.transaksi.index');
    }

    public function show($id)
    {
        $transaksi = Transaksi::with([
            'user',
            'order.meja',
            'order.items.menu.kategori',
            'booking.ruangan',
        ])->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil dimuat',
                'data' => $transaksi,
            ]);
        }

        return view('admin.transaksi.show', compact('transaksi'));
    }

    public function voidTransaksi(VoidTransaksiRequest $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status === 'void') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi sudah di-void sebelumnya',
            ], 422);
        }

        $transaksi->update([
            'status' => 'void',
            'alasan_void' => $request->alasan_void,
        ]);

        if ($transaksi->order) {
            app(StockService::class)->processVoidStock($transaksi->order);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil di-void',
            'data' => $transaksi,
        ]);
    }

    public function cetakStruk($id)
    {
        $transaksi = Transaksi::with([
            'user',
            'order.meja',
            'order.items.menu',
            'booking.ruangan',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.transaksi.struk', compact('transaksi'));

        return $pdf->download('struk-' . $transaksi->kode_transaksi . '.pdf');
    }
}
