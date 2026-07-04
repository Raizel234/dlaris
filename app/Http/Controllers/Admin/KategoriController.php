<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKategoriRequest;
use App\Http\Requests\Admin\UpdateKategoriRequest;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $perPage = request('per_page', 15);
            $kategoris = Kategori::withCount('menus')->latest()->paginate($perPage);
            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil dimuat',
                'data' => $kategoris->items(),
                'total' => $kategoris->total(),
                'per_page' => $kategoris->perPage(),
                'current_page' => $kategoris->currentPage(),
                'last_page' => $kategoris->lastPage(),
            ]);
        }
        return view('admin.kategori.index');
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function store(StoreKategoriRequest $request)
    {
        $data = $request->only(['nama', 'deskripsi', 'ikon']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('kategori', 'public');
        }

        $kategori = Kategori::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $kategori,
            ]);
        }

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data kategori berhasil dimuat',
                'data' => $kategori,
            ]);
        }

        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(UpdateKategoriRequest $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $data = $request->only(['nama', 'deskripsi', 'ikon']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('kategori', 'public');
        }

        $kategori->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui',
                'data' => $kategori,
            ]);
        }

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->menus()->exists()) {
            $msg = 'Kategori tidak dapat dihapus karena masih memiliki menu';
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
            return redirect()->route('admin.kategori.index')
                ->with('error', $msg);
        }

        $kategori->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus',
            ]);
        }

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus');
    }

    public function toggleStatus($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->update(['is_active' => !$kategori->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status kategori berhasil diubah',
            'data' => $kategori,
        ]);
    }
}
