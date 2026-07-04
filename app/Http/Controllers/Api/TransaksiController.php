<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        $transaksis = Transaksi::with('order.items.menu', 'booking.ruangan', 'user')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar transaksi berhasil diambil.',
            'data' => $transaksis,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = auth('sanctum')->user();

        $transaksi = Transaksi::with('order.items.menu', 'booking.ruangan', 'user')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail transaksi berhasil diambil.',
            'data' => $transaksi,
        ]);
    }
}
