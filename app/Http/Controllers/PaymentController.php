<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected MidtransService $midtrans,
        protected WhatsAppService $whatsapp,
    ) {}

    public function pay(Order $order)
    {
        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini sudah dibayar',
            ], 422);
        }

        try {
            $result = $this->midtrans->createTransaction($order);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran siap',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function notification(Request $request)
    {
        try {
            $order = $this->midtrans->handleNotification();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($order->payment_status === 'paid') {
                $this->whatsapp->sendOrderConfirmation($order);
            }

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function finish(Request $request)
    {
        $orderId = $request->order_id;
        $order = Order::where('nomor_order', $orderId)->first();

        return view('payment.finish', compact('order'));
    }

    public function unfinish(Request $request)
    {
        return view('payment.unfinish');
    }

    public function error(Request $request)
    {
        return view('payment.error');
    }
}
