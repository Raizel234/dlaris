<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function index(): JsonResponse
    {
        $menus = Menu::with('kategori')
            ->where('is_tersedia', true)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar menu berhasil diambil.',
            'data' => $menus,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $menu = Menu::with('kategori')
            ->where('is_tersedia', true)
            ->find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail menu berhasil diambil.',
            'data' => $menu,
        ]);
    }
}
