<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\KaraokeController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\LaporanController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,60');

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);

        // Menu
        Route::get('/menu', [MenuController::class, 'index']);
        Route::get('/menu/{id}', [MenuController::class, 'show']);
        Route::get('/kategori', [KategoriController::class, 'index']);

        // Order
        Route::post('/order', [OrderController::class, 'store']);
        Route::get('/order/{id}', [OrderController::class, 'show']);
        Route::get('/order/riwayat', [OrderController::class, 'riwayat']);
        Route::patch('/order/{id}/status', [OrderController::class, 'updateStatus']);

        // Karaoke
        Route::get('/karaoke/ruangan', [KaraokeController::class, 'ruangan']);
        Route::get('/karaoke/cek-ketersediaan', [KaraokeController::class, 'cekKetersediaan']);
        Route::post('/karaoke/booking', [KaraokeController::class, 'booking']);
        Route::get('/karaoke/booking/{id}', [KaraokeController::class, 'detailBooking']);
        Route::patch('/karaoke/booking/{id}/status', [KaraokeController::class, 'updateStatusBooking']);

        // Transaksi
        Route::get('/transaksi', [TransaksiController::class, 'index']);
        Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);

        // Laporan (admin only)
        Route::middleware('role:super_admin,admin')->group(function () {
            Route::get('/laporan/harian', [LaporanController::class, 'harian']);
            Route::get('/laporan/bulanan', [LaporanController::class, 'bulanan']);
        });
    });
});
