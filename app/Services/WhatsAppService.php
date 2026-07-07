<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $apiKey;
    protected ?string $sender;
    protected ?string $apiUrl;

    public function __construct()
    {
        $this->apiKey = Setting::getValue('wa_api_key', '');
        $this->sender = Setting::getValue('wa_sender', '');
        $this->apiUrl = Setting::getValue('wa_api_url', 'https://api.fonnte.com/send');
    }

    public function isEnabled(): bool
    {
        return !empty($this->apiKey);
    }

    public function sendMessage(string $phone, string $message): bool
    {
        if (!$this->isEnabled()) return false;

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post($this->apiUrl, [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $success = $response->successful();

            if (!$success) {
                Log::warning('WA send failed', [
                    'phone' => $phone,
                    'response' => $response->body(),
                ]);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('WA send error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendOrderConfirmation(Order $order): bool
    {
        $phone = $this->normalizePhone($order->no_hp);
        if (!$phone) return false;

        $tipeLabel = match ($order->tipe_pesanan) {
            'takeaway' => 'Take Away',
            'delivery' => 'Delivery',
            default => 'Dine In',
        };

        $items = '';
        foreach ($order->items as $i => $item) {
            $items .= ($i + 1) . ". {$item->menu?->nama} x{$item->jumlah} = Rp " . number_format($item->jumlah * $item->harga, 0, ',', '.') . "\n";
        }

        $message = "━━━ *D'LARIS Cafe & Karaoke* ━━━\n"
            . "✅ *PESANAN DITERIMA*\n"
            . "─────────────────\n"
            . "No. Order : *{$order->nomor_order}*\n"
            . "Tipe      : {$tipeLabel}\n"
            . "Atas Nama : {$order->nama_pelanggan}\n";

        if ($order->tipe_pesanan === 'delivery' && $order->alamat_pengiriman) {
            $message .= "Alamat    : {$order->alamat_pengiriman}\n";
        }

        $message .= "─────────────────\n"
            . "*PESANAN:*\n{$items}"
            . "─────────────────\n"
            . "Subtotal  : Rp " . number_format($order->total, 0, ',', '.') . "\n";

        if ($order->ongkir > 0) {
            $message .= "Ongkir    : Rp " . number_format($order->ongkir, 0, ',', '.') . "\n";
        }
        if ($order->service_charge > 0) {
            $message .= "Service   : Rp " . number_format($order->service_charge, 0, ',', '.') . "\n";
        }
        if ($order->pajak > 0) {
            $message .= "Pajak     : Rp " . number_format($order->pajak, 0, ',', '.') . "\n";
        }

        $message .= "─────────────────\n"
            . "*TOTAL* : Rp " . number_format($order->grand_total, 0, ',', '.') . "\n"
            . "─────────────────\n"
            . "Pesanan sedang diproses, akan kami informasikan kembali jika sudah siap.\n\n"
            . "Terima kasih 🙏\n"
            . "━━━━━━━━━━━━━━━━━━━";

        return $this->sendMessage($phone, $message);
    }

    public function sendOrderNotificationToStore(Order $order): bool
    {
        $storePhone = Setting::getValue('wa_store_number', '');
        if (empty($storePhone)) return false;

        $storePhone = $this->normalizePhone($storePhone);
        if (!$storePhone) return false;

        $tipeLabel = match ($order->tipe_pesanan) {
            'takeaway' => 'Take Away',
            'delivery' => 'Delivery',
            default => 'Dine In',
        };

        $items = '';
        foreach ($order->items as $i => $item) {
            $items .= ($i + 1) . ". {$item->menu?->nama} x{$item->jumlah} = Rp " . number_format($item->jumlah * $item->harga, 0, ',', '.') . "\n";
        }

        $message = "━━━ *D'LARIS Cafe & Karaoke* ━━━\n"
            . "🛒 *PESANAN BARU MASUK!*\n"
            . "─────────────────\n"
            . "No. Order : *{$order->nomor_order}*\n"
            . "Tipe      : {$tipeLabel}\n"
            . "Pelanggan : {$order->nama_pelanggan}\n"
            . "No. HP    : {$order->no_hp}\n";

        if ($order->tipe_pesanan === 'delivery' && $order->alamat_pengiriman) {
            $message .= "Alamat    : {$order->alamat_pengiriman}\n";
        }

        $message .= "─────────────────\n"
            . "*PESANAN:*\n{$items}"
            . "─────────────────\n"
            . "Subtotal  : Rp " . number_format($order->total, 0, ',', '.') . "\n";

        if ($order->ongkir > 0) {
            $message .= "Ongkir    : Rp " . number_format($order->ongkir, 0, ',', '.') . "\n";
        }
        if ($order->service_charge > 0) {
            $message .= "Service   : Rp " . number_format($order->service_charge, 0, ',', '.') . "\n";
        }
        if ($order->pajak > 0) {
            $message .= "Pajak     : Rp " . number_format($order->pajak, 0, ',', '.') . "\n";
        }

        $message .= "─────────────────\n"
            . "*TOTAL* : Rp " . number_format($order->grand_total, 0, ',', '.') . "\n"
            . "─────────────────\n"
            . "Silakan segera diproses! 🙏\n"
            . "━━━━━━━━━━━━━━━━━━━";

        return $this->sendMessage($storePhone, $message);
    }

    public function sendOrderStatus(Order $order): bool
    {
        $phone = $this->normalizePhone($order->no_hp);
        if (!$phone) return false;

        $statusLabel = match ($order->status) {
            'diproses' => '🔵 *SEDANG DIPROSES*',
            'selesai' => '🟢 *SIAP*',
            'dibatalkan' => '🔴 *DIBATALKAN*',
            default => "*{$order->status}*",
        };

        $message = "━━━ *D'LARIS Cafe & Karaoke* ━━━\n"
            . "📋 *UPDATE PESANAN*\n"
            . "─────────────────\n"
            . "No. Order : *{$order->nomor_order}*\n"
            . "Status    : {$statusLabel}\n";

        if ($order->status === 'selesai') {
            if ($order->tipe_pesanan === 'takeaway') {
                $message .= "\nPesanan Anda sudah siap diambil! Silakan datang ke kasir.";
            } elseif ($order->tipe_pesanan === 'delivery') {
                $message .= "\nPesanan Anda sedang dalam perjalanan!";
            }
        } elseif ($order->status === 'dibatalkan') {
            $message .= "\nMaaf, pesanan Anda dibatalkan. Silakan hubungi kami untuk info lebih lanjut.";
        }

        $message .= "\n\nTerima kasih 🙏\n"
            . "━━━━━━━━━━━━━━━━━━━";

        return $this->sendMessage($phone, $message);
    }

    protected function normalizePhone(?string $phone): ?string
    {
        if (!$phone) return null;

        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 2) === '62') {
            return $phone;
        }

        if (substr($phone, 0, 1) === '0') {
            return '62' . substr($phone, 1);
        }

        return '62' . $phone;
    }
}
