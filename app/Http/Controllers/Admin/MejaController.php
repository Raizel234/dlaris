<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\StoreMejaRequest;
use App\Http\Requests\Admin\UpdateMejaRequest;
use App\Http\Requests\Admin\UpdateStatusMejaRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MejaController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $perPage = request('per_page', 15);
            $mejas = Meja::latest()->paginate($perPage);
            return response()->json([
                'success' => true,
                'message' => 'Data meja berhasil dimuat',
                'data' => $mejas->items(),
                'total' => $mejas->total(),
                'per_page' => $mejas->perPage(),
                'current_page' => $mejas->currentPage(),
                'last_page' => $mejas->lastPage(),
            ]);
        }
        return view('admin.meja.index');
    }

    public function create()
    {
        return view('admin.meja.create');
    }

    public function store(StoreMejaRequest $request)
    {
        $data = $request->only(['nomor_meja', 'kapasitas', 'area', 'status']);
        $statusMap = ['dipakai' => 'terisi'];
        $data['status'] = $statusMap[$data['status']] ?? $data['status'];

        $meja = Meja::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Meja berhasil ditambahkan',
                'data' => $meja,
            ]);
        }

        return redirect()->route('admin.meja.index')
            ->with('success', 'Meja berhasil ditambahkan');
    }

    public function edit($id)
    {
        $meja = Meja::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data meja berhasil dimuat',
                'data' => $meja,
            ]);
        }

        return view('admin.meja.edit', compact('meja'));
    }

    public function update(UpdateMejaRequest $request, $id)
    {
        $meja = Meja::findOrFail($id);

        $data = $request->only(['nomor_meja', 'kapasitas', 'area', 'status']);
        $statusMap = ['dipakai' => 'terisi'];
        $data['status'] = $statusMap[$data['status']] ?? $data['status'];
        $meja->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Meja berhasil diperbarui',
                'data' => $meja,
            ]);
        }

        return redirect()->route('admin.meja.index')
            ->with('success', 'Meja berhasil diperbarui');
    }

    public function destroy($id)
    {
        $meja = Meja::findOrFail($id);

        if ($meja->orders()->exists()) {
            $msg = 'Meja tidak dapat dihapus karena masih memiliki transaksi';
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
            return redirect()->route('admin.meja.index')
                ->with('error', $msg);
        }

        $meja->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Meja berhasil dihapus',
            ]);
        }

        return redirect()->route('admin.meja.index')
            ->with('success', 'Meja berhasil dihapus');
    }

    public function updateStatus(UpdateStatusMejaRequest $request, $id)
    {
        $meja = Meja::findOrFail($id);
        $statusMap = ['dipakai' => 'terisi'];
        $status = $statusMap[$request->status] ?? $request->status;
        $meja->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => 'Status meja berhasil diubah',
            'data' => $meja,
        ]);
    }

    public function generateQR($id)
    {
        $meja = Meja::findOrFail($id);

        $url = route('pelanggan.menu', $meja->nomor_meja);
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate($url);

        $filename = 'qr/meja-' . $meja->nomor_meja . '.png';
        Storage::disk('public')->put($filename, $qrCode);

        $meja->update(['qr_code' => $filename]);

        return response()->json([
            'success' => true,
            'message' => 'QR Code berhasil dibuat',
            'data' => [
                'qr_url' => Storage::url($filename),
                'meja' => $meja,
            ],
        ]);
    }
}
