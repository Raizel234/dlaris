<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Ruangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaraokeController extends Controller
{
    public function ruangan(): JsonResponse
    {
        $ruangans = Ruangan::where('status', 'tersedia')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar ruangan berhasil diambil.',
            'data' => $ruangans,
        ]);
    }

    public function cekKetersediaan(Request $request): JsonResponse
    {
        $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'durasi' => 'required|integer|min:1',
        ]);

        $ruangan = Ruangan::findOrFail($request->ruangan_id);

        $jamMulai = $request->jam_mulai;
        $jamSelesai = date('H:i', strtotime($jamMulai . ' + ' . $request->durasi . ' hours'));

        $overlapping = Booking::where('ruangan_id', $request->ruangan_id)
            ->where('tanggal', $request->tanggal)
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                        ->where('jam_selesai', '>', $jamMulai);
                });
            })
            ->exists();

        $data = [
            'ruangan' => $ruangan,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'durasi' => $request->durasi,
            'tersedia' => !$overlapping,
            'total_harga' => $ruangan->tarif_per_jam * $request->durasi,
        ];

        return response()->json([
            'success' => true,
            'message' => $overlapping
                ? 'Ruangan tidak tersedia pada waktu tersebut.'
                : 'Ruangan tersedia untuk dipesan.',
            'data' => $data,
        ]);
    }

    public function booking(Request $request): JsonResponse
    {
        $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'durasi' => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:255',
        ]);

        $user = auth('sanctum')->user();
        $ruangan = Ruangan::findOrFail($request->ruangan_id);

        $jamMulai = $request->jam_mulai;
        $jamSelesai = date('H:i', strtotime($jamMulai . ' + ' . $request->durasi . ' hours'));
        $totalHarga = $ruangan->tarif_per_jam * $request->durasi;

        $overlapping = Booking::where('ruangan_id', $request->ruangan_id)
            ->where('tanggal', $request->tanggal)
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                        ->where('jam_selesai', '>', $jamMulai);
                });
            })
            ->exists();

        if ($overlapping) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan sudah dibooking pada waktu tersebut.',
                'data' => null,
            ], 409);
        }

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'user_id' => $user->id,
                'ruangan_id' => $request->ruangan_id,
                'nama_pemesan' => $user->name,
                'nomor_hp' => $user->nomor_hp,
                'tanggal' => $request->tanggal,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'durasi' => $request->durasi,
                'total_harga' => $totalHarga,
                'status' => 'pending',
                'catatan' => $request->catatan,
            ]);

            $booking->load('ruangan', 'user');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking ruangan berhasil dibuat.',
                'data' => $booking,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat booking: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function detailBooking(int $id): JsonResponse
    {
        $user = auth('sanctum')->user();

        $booking = Booking::with('ruangan', 'user')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail booking berhasil diambil.',
            'data' => $booking,
        ]);
    }

    public function updateStatusBooking(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,ongoing,completed,cancelled',
        ]);

        $booking = Booking::with('ruangan', 'user')->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $booking->update([
            'status' => $request->status,
        ]);

        $booking->refresh();
        $booking->load('ruangan', 'user');

        return response()->json([
            'success' => true,
            'message' => 'Status booking berhasil diperbarui.',
            'data' => $booking,
        ]);
    }
}
