<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'meja_id' => 'nullable|exists:mejas,id',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string|max:255',
        ]);

        $user = auth('sanctum')->user();

        $items = collect($request->items);
        $total = 0;
        $orderItems = [];

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                $subtotal = $menu->harga * $item['jumlah'];
                $total += $subtotal;

                $orderItems[] = new OrderItem([
                    'menu_id' => $menu->id,
                    'jumlah' => $item['jumlah'],
                    'harga' => $menu->harga,
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'meja_id' => $request->meja_id,
                'nomor_order' => 'ORD-' . strtoupper(\Str::random(8)),
                'status' => 'pending',
                'catatan' => $request->catatan,
                'total' => $total,
            ]);

            $order->items()->saveMany($orderItems);

            DB::commit();

            $order->load('items.menu', 'meja');

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $user = auth('sanctum')->user();

        $order = Order::with('items.menu', 'meja', 'user')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pesanan berhasil diambil.',
            'data' => $order,
        ]);
    }

    public function riwayat(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        $orders = Order::with('items.menu', 'meja')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pesanan berhasil diambil.',
            'data' => $orders,
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $order->update([
            'status' => $request->status,
        ]);

        $order->load('items.menu', 'meja');

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui.',
            'data' => $order,
        ]);
    }
}
