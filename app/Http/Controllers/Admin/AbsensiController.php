<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Http\Requests\Admin\StoreAbsensiRequest;
use App\Http\Requests\Admin\UpdateAbsensiRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $absensis = Absensi::with('user')
                ->when(request('tanggal'), fn($q) => $q->whereDate('tanggal', request('tanggal')))
                ->when(request('status'), fn($q) => $q->where('status', request('status')))
                ->when(request('user_id'), fn($q) => $q->where('user_id', request('user_id')))
                ->latest('tanggal')
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $absensis->items(),
                'total' => $absensis->total(),
                'per_page' => $absensis->perPage(),
                'current_page' => $absensis->currentPage(),
                'last_page' => $absensis->lastPage(),
            ]);
        }
        $karyawans = User::whereIn('role', ['super_admin', 'admin', 'kasir', 'karyawan'])->orderBy('name')->get();
        return view('admin.absensi.index', compact('karyawans'));
    }

    public function create()
    {
        $karyawans = User::whereIn('role', ['super_admin', 'admin', 'kasir', 'karyawan'])->orderBy('name')->get();
        return view('admin.absensi.create', compact('karyawans'));
    }

    public function store(StoreAbsensiRequest $request)
    {
        Absensi::updateOrCreate(
            ['user_id' => $request->user_id, 'tanggal' => $request->tanggal],
            [
                'jam_masuk' => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'verified_by' => auth()->id(),
            ]
        );

        return redirect()->route('admin.absensi.index')
            ->with('success', 'Absensi berhasil dicatat');
    }

    public function edit(Absensi $absensi)
    {
        $karyawans = User::whereIn('role', ['admin', 'kasir'])->orderBy('name')->get();
        return view('admin.absensi.edit', compact('absensi', 'karyawans'));
    }

    public function update(UpdateAbsensiRequest $request, Absensi $absensi)
    {
        $absensi->update($request->only(['jam_masuk', 'jam_pulang', 'status', 'keterangan']));

        return redirect()->route('admin.absensi.index')
            ->with('success', 'Absensi berhasil diperbarui');
    }

    public function destroy(Absensi $absensi)
    {
        $absensi->delete();
        return redirect()->route('admin.absensi.index')
            ->with('success', 'Absensi berhasil dihapus');
    }

    public function clockIn()
    {
        $today = now()->toDateString();
        $absensi = Absensi::firstOrCreate(
            ['user_id' => auth()->id(), 'tanggal' => $today],
            ['jam_masuk' => now()->format('H:i'), 'status' => 'hadir']
        );

        if (!$absensi->jam_masuk) {
            $absensi->update(['jam_masuk' => now()->format('H:i'), 'status' => 'hadir']);
        }

        return response()->json(['success' => true, 'data' => $absensi]);
    }

    public function clockOut()
    {
        $today = now()->toDateString();
        $absensi = Absensi::where('user_id', auth()->id())->whereDate('tanggal', $today)->first();

        if ($absensi) {
            $absensi->update(['jam_pulang' => now()->format('H:i')]);
        }

        return response()->json(['success' => true, 'data' => $absensi]);
    }

    public function cekStatus()
    {
        $today = now()->toDateString();
        $absensi = Absensi::where('user_id', auth()->id())->whereDate('tanggal', $today)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'has_clocked_in' => $absensi && $absensi->jam_masuk,
                'has_clocked_out' => $absensi && $absensi->jam_pulang,
                'absensi' => $absensi,
            ],
        ]);
    }
}
