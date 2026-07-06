<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createTransaction(Order $order): array
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'id' => 'MENU-' . $item->menu_id,
                'price' => (int) $item->harga,
                'quantity' => $item->jumlah,
                'name' => $item->menu?->nama ?? 'Menu #' . $item->menu_id,
            ];
        }

        if ($order->ongkir > 0) {
            $items[] = [
                'id' => 'ONGKIR',
                'price' => (int) $order->ongkir,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
        }

        if ($order->service_charge > 0) {
            $items[] = [
                'id' => 'SERVICE',
                'price' => (int) $order->service_charge,
                'quantity' => 1,
                'name' => 'Service Charge',
            ];
        }

        if ($order->pajak > 0) {
            $items[] = [
                'id' => 'PAJAK',
                'price' => (int) $order->pajak,
                'quantity' => 1,
                'name' => 'Pajak',
            ];
        }

        $customerDetails = [
            'first_name' => $order->nama_pelanggan ?? $order->user?->name ?? 'Pelanggan',
            'phone' => $order->no_hp ?? $order->user?->nomor_hp ?? '',
        ];

        $params = [
            'transaction_details' => [
                'order_id' => $order->nomor_order,
                'gross_amount' => (int) $order->grand_total,
            ],
            'item_details' => $items,
            'customer_details' => $customerDetails,
            'callbacks' => [
                'finish' => route('payment.finish'),
                'unfinish' => route('payment.unfinish'),
                'error' => route('payment.error'),
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        $order->update([
            'snap_token' => $snapToken,
            'payment_method' => 'midtrans',
            'payment_status' => 'pending',
        ]);

        return [
            'snap_token' => $snapToken,
            'client_key' => Config::$clientKey,
            'is_production' => Config::$isProduction,
        ];
    }

    public function handleNotification(): ?Order
    {
        $notif = new Notification();
        $transactionStatus = $notif->transaction_status;
        $fraudStatus = $notif->fraud_status;
        $orderId = $notif->order_id;

        $order = Order::where('nomor_order', $orderId)->first();
        if (!$order) return null;

        $status = match (true) {
            $transactionStatus == 'capture' && $fraudStatus == 'accept' => 'paid',
            $transactionStatus == 'settlement' => 'paid',
            $transactionStatus == 'pending' => 'pending',
            $transactionStatus == 'deny' || $transactionStatus == 'cancel' || $transactionStatus == 'expire' => 'failed',
            default => $order->payment_status,
        };

        $order->update(['payment_status' => $status]);

        if ($status === 'paid') {
            $serviceChargePersen = (float) \App\Models\Setting::getValue('service_charge', 0);
            $pajakPersen = (float) \App\Models\Setting::getValue('pajak', 0);
            $order->hitungGrandTotal($serviceChargePersen, $pajakPersen)->save();
        }

        return $order;
    }
}
