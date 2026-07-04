<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Ruangan;
use App\Http\Requests\Admin\StoreRuanganRequest;
use App\Http\Requests\Admin\UpdateRuanganRequest;
use App\Http\Requests\Admin\UpdateStatusBookingRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KaraokeController extends Controller
{
    public function indexRuangan()
    {
        $this->autoCompleteExpiredBookings();
        if (request()->ajax()) {
            $perPage = request('per_page', 15);
            $ruangans = Ruangan::withCount('bookingsAktif')->latest()->paginate($perPage);
            return response()->json([
                'success' => true,
                'message' => 'Data ruangan berhasil dimuat',
                'data' => $ruangans->items(),
                'total' => $ruangans->total(),
                'per_page' => $ruangans->perPage(),
                'current_page' => $ruangans->currentPage(),
                'last_page' => $ruangans->lastPage(),
            ]);
        }
        return view('admin.ruangan.index');
    }

    public function createRuangan()
    {
        return view('admin.ruangan.create');
    }

    public function storeRuangan(StoreRuanganRequest $request)
    {
        $data = $request->only(['nama', 'kapasitas', 'tarif_per_jam', 'fasilitas', 'status']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('karaoke', 'public');
        }

        $ruangan = Ruangan::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ruangan berhasil ditambahkan',
                'data' => $ruangan,
            ]);
        }

        return redirect()->route('admin.ruangan.index')
            ->with('success', 'Ruangan berhasil ditambahkan');
    }

    public function editRuangan($id)
    {
        $ruangan = Ruangan::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data ruangan berhasil dimuat',
                'data' => $ruangan,
            ]);
        }

        return view('admin.ruangan.edit', compact('ruangan'));
    }

    public function updateRuangan(UpdateRuanganRequest $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $data = $request->only(['nama', 'kapasitas', 'tarif_per_jam', 'fasilitas', 'status']);

        if ($request->hasFile('foto')) {
            if ($ruangan->foto) {
                Storage::disk('public')->delete($ruangan->foto);
            }
            $data['foto'] = $request->file('foto')->store('karaoke', 'public');
        }

        $ruangan->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ruangan berhasil diperbarui',
                'data' => $ruangan,
            ]);
        }

        return redirect()->route('admin.ruangan.index')
            ->with('success', 'Ruangan berhasil diperbarui');
    }

    public function destroyRuangan($id)
    {
        $ruangan = Ruangan::findOrFail($id);

        if ($ruangan->bookings()->exists()) {
            $msg = 'Ruangan tidak dapat dihapus karena masih memiliki booking';
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
            return redirect()->route('admin.ruangan.index')
                ->with('error', $msg);
        }

        if ($ruangan->foto) {
            Storage::disk('public')->delete($ruangan->foto);
        }

        $ruangan->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ruangan berhasil dihapus',
            ]);
        }

        return redirect()->route('admin.ruangan.index')
            ->with('success', 'Ruangan berhasil dihapus');
    }

    public function indexBooking()
    {
        $this->autoCompleteExpiredBookings();
        if (request()->ajax()) {
            $bookings = Booking::with(['user', 'ruangan'])->latest()->get();
            return response()->json([
                'success' => true,
                'message' => 'Data booking berhasil dimuat',
                'data' => $bookings,
            ]);
        }
        return view('admin.booking.index');
    }

    public function calendarBooking()
    {
        $ruangans = Ruangan::orderBy('nama')->get();
        return view('admin.booking.calendar', compact('ruangans'));
    }

    public function calendarData(Request $request)
    {
        $query = Booking::with(['ruangan:id,nama', 'user:id,name'])
            ->whereIn('status', ['pending', 'confirmed', 'ongoing']);

        if ($request->ruangan_id) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($b) {
            $colors = [
                'pending' => '#f59e0b',
                'confirmed' => '#3b82f6',
                'ongoing' => '#10b981',
            ];
            $start = $b->tanggal->format('Y-m-d') . 'T' . substr($b->jam_mulai, 0, 5);
            $end = $b->tanggal->format('Y-m-d') . 'T' . substr($b->jam_selesai, 0, 5);

            if ($end <= $start) {
                $end = $b->tanggal->addDay()->format('Y-m-d') . 'T' . substr($b->jam_selesai, 0, 5);
            }

            return [
                'id' => $b->id,
                'title' => $b->ruangan->nama . ' - ' . $b->nama_pemesan,
                'start' => $start,
                'end' => $end,
                'backgroundColor' => $colors[$b->status] ?? '#6b7280',
                'borderColor' => $colors[$b->status] ?? '#6b7280',
                'textColor' => '#fff',
                'extendedProps' => [
                    'status' => $b->status,
                    'ruangan' => $b->ruangan->nama,
                    'pemesan' => $b->nama_pemesan,
                    'durasi' => $b->durasi,
                    'total_harga' => (float) $b->total_harga,
                    'tanggal' => $b->tanggal->format('d/m/Y'),
                    'jam_mulai' => substr($b->jam_mulai, 0, 5),
                    'jam_selesai' => substr($b->jam_selesai, 0, 5),
                ],
            ];
        });

        return response()->json($events);
    }

    public function showBooking($id)
    {
        $booking = Booking::with(['user', 'ruangan', 'transaksi'])->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data booking berhasil dimuat',
                'data' => $booking,
            ]);
        }

        return view('admin.booking.show', compact('booking'));
    }

    public function updateStatusBooking(UpdateStatusBookingRequest $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Update room status based on booking status
        if (in_array($request->status, ['ongoing', 'confirmed'])) {
            $booking->ruangan->update(['status' => 'digunakan']);
        } elseif (in_array($request->status, ['selesai', 'dibatalkan'])) {
            $booking->ruangan->update(['status' => 'tersedia']);
        }

        $booking->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status booking berhasil diubah',
            'data' => $booking,
        ]);
    }

    private function autoCompleteExpiredBookings(): void
    {
        $expired = Booking::whereIn('status', ['ongoing', 'confirmed'])
            ->where(function ($q) {
                $q->whereDate('tanggal', '<', now()->toDateString())
                  ->orWhere(function ($q2) {
                      $q2->whereDate('tanggal', '=', now()->toDateString())
                         ->whereTime('jam_selesai', '<=', now()->toTimeString());
                  });
            })
            ->get();

        foreach ($expired as $booking) {
            $booking->ruangan->update(['status' => 'tersedia']);
            $booking->update(['status' => 'selesai']);
        }
    }
}
