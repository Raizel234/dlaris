<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Menu;
use App\Http\Requests\Admin\StoreMenuRequest;
use App\Http\Requests\Admin\UpdateMenuRequest;
use App\Http\Requests\Admin\UploadFotoMenuRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = Menu::with('kategori')->latest();

            if ($search = request('search')) {
                $query->where('nama', 'like', "%{$search}%");
            }
            if ($kategori = request('kategori')) {
                $query->where('kategori_id', $kategori);
            }

            $perPage = request('per_page', 15);
            $menus = $query->paginate($perPage);
            return response()->json([
                'success' => true,
                'message' => 'Data menu berhasil dimuat',
                'data' => $menus->items(),
                'total' => $menus->total(),
                'per_page' => $menus->perPage(),
                'current_page' => $menus->currentPage(),
                'last_page' => $menus->lastPage(),
            ]);
        }
        return view('admin.menu.index');
    }

    public function create()
    {
        $kategoris = Kategori::where('is_active', true)->get();
        return view('admin.menu.create', compact('kategoris'));
    }

    public function store(StoreMenuRequest $request)
    {
        $data = $request->only([
            'kategori_id', 'nama', 'deskripsi', 'harga', 'stok',
            'is_tersedia', 'is_best_seller', 'is_new',
        ]);

        $data['is_tersedia'] = $request->boolean('is_tersedia');
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['is_new'] = $request->boolean('is_new');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        }

        $menu = Menu::create($data);
        $menu->load('kategori');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil ditambahkan',
                'data' => $menu,
            ]);
        }

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu berhasil ditambahkan');
    }

    public function edit($id)
    {
        $menu = Menu::with('kategori')->findOrFail($id);
        $kategoris = Kategori::where('is_active', true)->get();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data menu berhasil dimuat',
                'data' => $menu,
            ]);
        }

        return view('admin.menu.edit', compact('menu', 'kategoris'));
    }

    public function update(UpdateMenuRequest $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $data = $request->only([
            'kategori_id', 'nama', 'deskripsi', 'harga', 'stok',
            'is_tersedia', 'is_best_seller', 'is_new',
        ]);

        $data['is_tersedia'] = $request->boolean('is_tersedia');
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['is_new'] = $request->boolean('is_new');

        if ($request->hasFile('foto')) {
            if ($menu->foto) {
                Storage::disk('public')->delete($menu->foto);
            }
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        }

        $menu->update($data);
        $menu->load('kategori');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil diperbarui',
                'data' => $menu,
            ]);
        }

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu berhasil diperbarui');
    }

    public function destroy($id)
    {
        $menu = Menu::withCount(['orderItems', 'ratings'])->findOrFail($id);

        if ($menu->order_items_count > 0 || $menu->ratings_count > 0) {
            $msg = 'Menu tidak dapat dihapus karena masih memiliki transaksi atau ulasan';
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('admin.menu.index')->with('error', $msg);
        }

        $menu->bahans()->detach();

        if ($menu->foto) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menu->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dihapus',
            ]);
        }

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu berhasil dihapus');
    }

    public function toggleTersedia($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update(['is_tersedia' => !$menu->is_tersedia]);

        return response()->json([
            'success' => true,
            'message' => 'Status ketersediaan menu berhasil diubah',
            'data' => $menu,
        ]);
    }

    public function toggleBestSeller($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update(['is_best_seller' => !$menu->is_best_seller]);

        return response()->json([
            'success' => true,
            'message' => 'Status best seller menu berhasil diubah',
            'data' => $menu,
        ]);
    }

    public function toggleNew($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update(['is_new' => !$menu->is_new]);

        return response()->json([
            'success' => true,
            'message' => 'Status new menu berhasil diubah',
            'data' => $menu,
        ]);
    }

    public function uploadFoto(UploadFotoMenuRequest $request)
    {
        $path = $request->file('foto')->store('menu', 'public');

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil diupload',
            'data' => ['url' => Storage::url($path)],
        ]);
    }
}
