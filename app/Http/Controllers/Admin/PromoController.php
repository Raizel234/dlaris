<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePromoRequest;
use App\Http\Requests\Admin\UpdatePromoRequest;
use App\Http\Requests\Admin\ValidasiPromoRequest;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $promos = Promo::latest()->paginate(10);
            return response()->json([
                'success' => true,
                'data' => $promos->items(),
                'total' => $promos->total(),
                'per_page' => $promos->perPage(),
                'current_page' => $promos->currentPage(),
                'last_page' => $promos->lastPage(),
            ]);
        }
        return view('admin.promo.index');
    }

    public function create()
    {
        return view('admin.promo.create');
    }

    public function store(StorePromoRequest $request)
    {
        $data = $request->only([
            'kode', 'nama', 'deskripsi', 'tipe', 'nilai',
            'min_belanja', 'maks_diskon', 'kuota',
            'berlaku_mulai', 'berlaku_sampai', 'is_active',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        Promo::create($data);

        return redirect()->route('admin.promo.index')
            ->with('success', 'Promo berhasil ditambahkan');
    }

    public function edit(Promo $promo)
    {
        return view('admin.promo.edit', compact('promo'));
    }

    public function update(UpdatePromoRequest $request, Promo $promo)
    {
        $data = $request->only([
            'kode', 'nama', 'deskripsi', 'tipe', 'nilai',
            'min_belanja', 'maks_diskon', 'kuota',
            'berlaku_mulai', 'berlaku_sampai', 'is_active',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $promo->update($data);

        return redirect()->route('admin.promo.index')
            ->with('success', 'Promo berhasil diperbarui');
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();
        return redirect()->route('admin.promo.index')
            ->with('success', 'Promo berhasil dihapus');
    }

    public function toggle(Promo $promo)
    {
        $promo->update(['is_active' => !$promo->is_active]);
        return response()->json(['success' => true, 'data' => $promo]);
    }

    public function validasi(ValidasiPromoRequest $request)
    {
        $promo = Promo::where('kode', $request->kode)->first();

        if (!$promo || !$promo->isValid($request->total, $request->metode_bayar)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo tidak valid atau sudah kadaluarsa',
            ], 422);
        }

        $diskon = $promo->hitungDiskon($request->total);

        return response()->json([
            'success' => true,
            'message' => 'Promo berhasil diterapkan',
            'data' => [
                'promo' => $promo,
                'diskon' => $diskon,
                'total_setelah_diskon' => $request->total - $diskon,
            ],
        ]);
    }
}
