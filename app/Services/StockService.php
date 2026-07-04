<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Menu;
use App\Models\Bahan;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    protected bool $autoEnabled;

    public function __construct()
    {
        $setting = Setting::first();
        $this->autoEnabled = $setting && $setting->auto_stock_deduction;
    }

    public function isAutoEnabled(): bool
    {
        return $this->autoEnabled;
    }

    public function deductMenuStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $menu = $item->menu;
            if ($menu && $menu->stok !== null && $menu->stok > 0) {
                $menu->decrement('stok', $item->jumlah);
            }
        }
    }

    public function restoreMenuStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $menu = $item->menu;
            if ($menu && $menu->stok !== null) {
                $menu->increment('stok', $item->jumlah);
            }
        }
    }

    public function deductBahanStock(Order $order): void
    {
        if (!$this->autoEnabled) return;

        DB::beginTransaction();
        try {
            foreach ($order->items as $item) {
                if (!$item->menu) continue;

                $menu = $item->menu;
                $jumlahPesanan = $item->jumlah;

                foreach ($menu->bahans as $bahan) {
                    $jumlahPerPorsi = $bahan->pivot->jumlah_per_porsi ?? 1;
                    $totalDibutuhkan = $jumlahPerPorsi * $jumlahPesanan;

                    if ($bahan->stok >= $totalDibutuhkan) {
                        $bahan->decrement('stok', $totalDibutuhkan);
                    } else {
                        $bahan->update(['stok' => 0]);
                        Log::warning("Stok bahan {$bahan->nama} (ID: {$bahan->id}) tidak mencukupi untuk menu {$menu->nama}. Sisa stok: {$bahan->stok}, dibutuhkan: {$totalDibutuhkan}");
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal mengurangi stok bahan untuk order {$order->id}: {$e->getMessage()}");
        }
    }

    public function restoreBahanStock(Order $order): void
    {
        DB::beginTransaction();
        try {
            foreach ($order->items as $item) {
                if (!$item->menu) continue;

                $menu = $item->menu;
                $jumlahPesanan = $item->jumlah;

                foreach ($menu->bahans as $bahan) {
                    $jumlahPerPorsi = $bahan->pivot->jumlah_per_porsi ?? 1;
                    $totalDikembalikan = $jumlahPerPorsi * $jumlahPesanan;

                    $bahan->increment('stok', $totalDikembalikan);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal mengembalikan stok bahan untuk order {$order->id}: {$e->getMessage()}");
        }
    }

    public function processPaymentStock(Order $order): void
    {
        if ($this->autoEnabled) {
            $this->deductBahanStock($order);
        }
    }

    public function processVoidStock(Order $order): void
    {
        $this->restoreBahanStock($order);
        $this->restoreMenuStock($order);
    }
}
