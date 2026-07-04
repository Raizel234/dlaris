<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePengeluaranRequest;
use App\Http\Requests\Admin\UpdatePengeluaranRequest;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $pengeluarans = Pengeluaran::with('createdBy')
                ->when(request('dari'), fn($q) => $q->whereDate('tanggal', '>=', request('dari')))
                ->when(request('sampai'), fn($q) => $q->whereDate('tanggal', '<=', request('sampai')))
                ->when(request('kategori'), fn($q) => $q->where('kategori', request('kategori')))
                ->latest('tanggal')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $pengeluarans->items(),
                'total' => $pengeluarans->total(),
                'per_page' => $pengeluarans->perPage(),
                'current_page' => $pengeluarans->currentPage(),
                'last_page' => $pengeluarans->lastPage(),
                'total_jumlah' => (float) $pengeluarans->getCollection()->sum('jumlah'),
            ]);
        }
        return view('admin.pengeluaran.index');
    }

    public function create()
    {
        return view('admin.pengeluaran.create');
    }

    public function store(StorePengeluaranRequest $request)
    {
        $data = $request->only(['kategori', 'judul', 'deskripsi', 'jumlah', 'tanggal']);
        $data['created_by'] = auth()->id();

        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('pengeluaran', 'public');
        }

        Pengeluaran::create($data);

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil dicatat');
    }

    public function edit(Pengeluaran $pengeluaran)
    {
        return view('admin.pengeluaran.edit', compact('pengeluaran'));
    }

    public function update(UpdatePengeluaranRequest $request, Pengeluaran $pengeluaran)
    {
        $data = $request->only(['kategori', 'judul', 'deskripsi', 'jumlah', 'tanggal']);

        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('pengeluaran', 'public');
        }

        $pengeluaran->update($data);

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil diperbarui');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        $pengeluaran->delete();
        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil dihapus');
    }
}
