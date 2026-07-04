<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Http\Requests\Admin\StoreBahanRequest;
use App\Http\Requests\Admin\UpdateBahanRequest;
use App\Http\Requests\Admin\StokMasukBahanRequest;
use App\Models\Menu;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $bahans = Bahan::with('menus')
                ->when(request('search'), fn($q) => $q->where('nama', 'like', '%' . request('search') . '%'))
                ->when(request('low_stock'), fn($q) => $q->whereColumn('stok', '<=', 'stok_minimum'))
                ->latest()
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $bahans->items(),
                'total' => $bahans->total(),
                'per_page' => $bahans->perPage(),
                'current_page' => $bahans->currentPage(),
                'last_page' => $bahans->lastPage(),
            ]);
        }
        return view('admin.bahan.index');
    }

    public function create()
    {
        $menus = Menu::orderBy('nama')->get();
        return view('admin.bahan.create', compact('menus'));
    }

    public function store(StoreBahanRequest $request)
    {
        $bahan = Bahan::create($request->only([
            'nama', 'satuan', 'stok', 'stok_minimum', 'harga_beli', 'supplier', 'keterangan',
        ]));

        if ($request->menus) {
            $sync = [];
            foreach ($request->menus as $menu) {
                if (!empty($menu['id']) && $menu['jumlah'] > 0) {
                    $sync[$menu['id']] = ['jumlah_per_porsi' => $menu['jumlah']];
                }
            }
            $bahan->menus()->sync($sync);
        }

        return redirect()->route('admin.bahan.index')
            ->with('success', 'Bahan berhasil ditambahkan');
    }

    public function edit(Bahan $bahan)
    {
        $menus = Menu::orderBy('nama')->get();
        $bahan->load('menus');
        return view('admin.bahan.edit', compact('bahan', 'menus'));
    }

    public function update(UpdateBahanRequest $request, Bahan $bahan)
    {
        $bahan->update($request->only([
            'nama', 'satuan', 'stok', 'stok_minimum', 'harga_beli', 'supplier', 'keterangan',
        ]));

        if ($request->menus) {
            $sync = [];
            foreach ($request->menus as $menu) {
                if (!empty($menu['id']) && $menu['jumlah'] > 0) {
                    $sync[$menu['id']] = ['jumlah_per_porsi' => $menu['jumlah']];
                }
            }
            $bahan->menus()->sync($sync);
        }

        return redirect()->route('admin.bahan.index')
            ->with('success', 'Bahan berhasil diperbarui');
    }

    public function destroy(Bahan $bahan)
    {
        $bahan->menus()->detach();
        $bahan->delete();
        return redirect()->route('admin.bahan.index')
            ->with('success', 'Bahan berhasil dihapus');
    }

    public function stokMasuk(StokMasukBahanRequest $request, Bahan $bahan)
    {
        $bahan->increment('stok', $request->jumlah);
        return redirect()->route('admin.bahan.index')
            ->with('success', "Stok {$bahan->nama} ditambah {$request->jumlah} {$bahan->satuan}");
    }
}
