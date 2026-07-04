<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;

class KategoriController extends Controller
{
    public function index(): JsonResponse
    {
        $kategoris = Kategori::where('is_active', true)
            ->with('menusAktif')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori berhasil diambil.',
            'data' => $kategoris,
        ]);
    }
}
