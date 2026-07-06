<?php

use Illuminate\Support\Facades\Artisan;

Route::get('/migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
    return nl2br(Artisan::output());
});

Route::get('/seed', function () {
    Artisan::call('db:seed', ['--force' => true]);
    return nl2br(Artisan::output());
});

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController as AdminKategoriController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\MejaController as AdminMejaController;
use App\Http\Controllers\Admin\KaraokeController as AdminKaraokeController;
use App\Http\Controllers\Admin\TransaksiController as AdminTransaksiController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\PelangganController as AdminPelangganController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\PengeluaranController;
use App\Http\Controllers\Admin\BahanController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KaryawanController as KaryawanDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('beranda');

// Admin & Kasir Routes
Route::middleware(['auth', 'verified', 'log.activity'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/dashboard/chart', [DashboardController::class, 'chartData'])->name('dashboard.chart');
    Route::get('/dashboard/top-menus', [DashboardController::class, 'topMenus'])->name('dashboard.top-menus');

    // Karyawan-specific features (before resource routes to avoid wildcard conflict)
    Route::middleware('role:karyawan')->group(function () {
        Route::get('karyawan/dashboard', [KaryawanDashboardController::class, 'dashboard'])->name('karyawan.dashboard');
        Route::get('karyawan/menu', [KaryawanDashboardController::class, 'menu'])->name('karyawan.menu');
        Route::get('karyawan/booking', [KaryawanDashboardController::class, 'booking'])->name('karyawan.booking');
        Route::get('karyawan/pesanan', [KaryawanDashboardController::class, 'pesanan'])->name('karyawan.pesanan');
    });

    Route::middleware('role:super_admin,admin')->group(function () {
        // Kategori
        Route::resource('kategori', AdminKategoriController::class);
        Route::patch('kategori/{kategori}/toggle', [AdminKategoriController::class, 'toggleStatus'])->name('kategori.toggle');

        // Menu
        Route::resource('menu', AdminMenuController::class);
        Route::patch('menu/{menu}/toggle-tersedia', [AdminMenuController::class, 'toggleTersedia'])->name('menu.toggle-tersedia');
        Route::patch('menu/{menu}/toggle-best-seller', [AdminMenuController::class, 'toggleBestSeller'])->name('menu.toggle-best-seller');
        Route::patch('menu/{menu}/toggle-new', [AdminMenuController::class, 'toggleNew'])->name('menu.toggle-new');
        Route::post('menu/upload-foto', [AdminMenuController::class, 'uploadFoto'])->name('menu.upload-foto');

        // Meja
        Route::resource('meja', AdminMejaController::class);
        Route::patch('meja/{meja}/status', [AdminMejaController::class, 'updateStatus'])->name('meja.status');
        Route::post('meja/{meja}/generate-qr', [AdminMejaController::class, 'generateQR'])->name('meja.generate-qr');

        // Karaoke Ruangan
        Route::get('ruangan', [AdminKaraokeController::class, 'indexRuangan'])->name('ruangan.index');
        Route::get('ruangan/create', [AdminKaraokeController::class, 'createRuangan'])->name('ruangan.create');
        Route::post('ruangan', [AdminKaraokeController::class, 'storeRuangan'])->name('ruangan.store');
        Route::get('ruangan/{ruangan}/edit', [AdminKaraokeController::class, 'editRuangan'])->name('ruangan.edit');
        Route::match(['put', 'patch'], 'ruangan/{ruangan}', [AdminKaraokeController::class, 'updateRuangan'])->name('ruangan.update');
        Route::delete('ruangan/{ruangan}', [AdminKaraokeController::class, 'destroyRuangan'])->name('ruangan.destroy');
        Route::get('booking', [AdminKaraokeController::class, 'indexBooking'])->name('booking.index');
        Route::get('booking/calendar', [AdminKaraokeController::class, 'calendarBooking'])->name('booking.calendar');
        Route::get('booking/calendar-data', [AdminKaraokeController::class, 'calendarData'])->name('booking.calendar-data');
        Route::get('booking/{booking}', [AdminKaraokeController::class, 'showBooking'])->name('booking.show');
        Route::patch('booking/{booking}/status', [AdminKaraokeController::class, 'updateStatusBooking'])->name('booking.status');

        // Laporan
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/harian', [LaporanController::class, 'harian'])->name('laporan.harian');
        Route::get('laporan/bulanan', [LaporanController::class, 'bulanan'])->name('laporan.bulanan');
        Route::get('laporan/tahunan', [LaporanController::class, 'tahunan'])->name('laporan.tahunan');
        Route::get('laporan/per-kategori', [LaporanController::class, 'perKategori'])->name('laporan.per-kategori');
        Route::get('laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
        Route::get('laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');

        // Transaksi (void only — index/show/cetak shared below)
        Route::post('transaksi/{transaksi}/void', [AdminTransaksiController::class, 'voidTransaksi'])->name('transaksi.void');

        // Promo
        Route::resource('promo', PromoController::class);
        Route::patch('promo/{promo}/toggle', [PromoController::class, 'toggle'])->name('promo.toggle');
        Route::post('promo/validasi', [PromoController::class, 'validasi'])->name('promo.validasi');

        // Pelanggan
        Route::get('pelanggan', [AdminPelangganController::class, 'index'])->name('pelanggan.index');
        Route::get('pelanggan/{id}', [AdminPelangganController::class, 'show'])->name('pelanggan.show');

        // Absensi (management only — index/clock shared below)
        Route::get('absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
        Route::post('absensi', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::get('absensi/{absensi}/edit', [AbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('absensi/{absensi}', [AbsensiController::class, 'update'])->name('absensi.update');
        Route::delete('absensi/{absensi}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');

        // Pengeluaran
        Route::resource('pengeluaran', PengeluaranController::class);

        // Bahan
        Route::resource('bahan', BahanController::class);
        Route::post('bahan/{bahan}/stok-masuk', [BahanController::class, 'stokMasuk'])->name('bahan.stok-masuk');

    });

    // Super Admin only
    Route::middleware('role:super_admin')->group(function () {
        // Karyawan
        Route::resource('karyawan', KaryawanController::class);

        // Settings
        Route::get('pengaturan', [SettingController::class, 'index'])->name('pengaturan');
        Route::post('pengaturan', [SettingController::class, 'update'])->name('pengaturan.update');

        // Activity Log
        Route::get('activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');

        // Backup
        Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
        Route::post('backup', [BackupController::class, 'create'])->name('backup.create');
        Route::get('backup/{filename}/download', [BackupController::class, 'download'])->name('backup.download')->where('filename', '^[\w.-]+\.sql$');
        Route::delete('backup/{filename}', [BackupController::class, 'destroy'])->name('backup.destroy')->where('filename', '^[\w.-]+\.sql$');
    });

    // Kasir-only routes (POS)
    Route::middleware('role:kasir')->group(function () {
        Route::get('pos', [KasirController::class, 'index'])->name('pos');

        // Page views (redirect to combined POS page with tab parameter)
        Route::redirect('pos/menu-page', 'pos?tab=menu')->name('pos.menu-page');
        Route::redirect('pos/keranjang-page', 'pos?tab=keranjang')->name('pos.keranjang-page');
        Route::redirect('pos/pesanan-page', 'pos?tab=pesanan')->name('pos.pesanan-page');
        Route::redirect('pos/karaoke-page', 'pos?tab=karaoke')->name('pos.karaoke-page');

        Route::get('pos/menu/{kategori}', [KasirController::class, 'getMenuByKategori'])->name('pos.menu');
        Route::get('pos/search-menu', [KasirController::class, 'searchMenu'])->name('pos.search-menu');
        Route::get('pos/meja', [KasirController::class, 'getMeja'])->name('pos.meja');
        Route::post('pos/cart/add', [KasirController::class, 'addItem'])->name('pos.cart.add');
        Route::post('pos/cart/update', [KasirController::class, 'updateItem'])->name('pos.cart.update');
        Route::post('pos/cart/remove', [KasirController::class, 'removeItem'])->name('pos.cart.remove');
        Route::get('pos/cart', [KasirController::class, 'getCart'])->name('pos.cart');
        Route::post('pos/cart/clear', [KasirController::class, 'clearCart'])->name('pos.cart.clear');
        Route::post('pos/order', [KasirController::class, 'createOrder'])->name('pos.order');
        Route::get('pos/orders', [KasirController::class, 'getOrders'])->name('pos.orders');
        Route::patch('pos/order/{order}/status', [KasirController::class, 'updateStatusOrder'])->name('pos.order.status');
        Route::get('pos/order/{order}/pembayaran', [KasirController::class, 'formPembayaran'])->name('pos.pembayaran');
        Route::post('pos/order/{order}/bayar', [KasirController::class, 'pembayaran'])->name('pos.bayar');
        Route::post('pos/hitung-kembalian', [KasirController::class, 'hitungKembalian'])->name('pos.hitung-kembalian');
        Route::get('pos/order/{order}/cetak', [KasirController::class, 'cetakStruk'])->name('pos.cetak');
        Route::get('pos/notifications', [KasirController::class, 'getNotifications'])->name('pos.notifications');
        Route::post('pos/booking-karaoke', [KasirController::class, 'bookingKaraoke'])->name('pos.booking-karaoke');
        Route::get('pos/booking-aktif', [KasirController::class, 'getActiveBookings'])->name('pos.booking-aktif');
        Route::post('pos/booking/{booking}/start', [KasirController::class, 'startTimer'])->name('pos.booking.start');
        Route::post('pos/booking/{booking}/extend', [KasirController::class, 'extendSession'])->name('pos.booking.extend');
        Route::get('pos/booking/{booking}/timer', [KasirController::class, 'getTimerStatus'])->name('pos.booking.timer');

        // Split payment
        Route::post('pos/order/{order}/split-bayar', [KasirController::class, 'splitPembayaran'])->name('pos.split-bayar');
    });

    // Shared routes (Absensi & Transaksi read-only) — All admin roles
    Route::middleware('role:super_admin,admin,kasir,karyawan')->group(function () {
        Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');

        Route::post('absensi/clock-in', [AbsensiController::class, 'clockIn'])->name('absensi.clock-in');
        Route::post('absensi/clock-out', [AbsensiController::class, 'clockOut'])->name('absensi.clock-out');
        Route::get('absensi/cek-status', [AbsensiController::class, 'cekStatus'])->name('absensi.cek-status');

        Route::get('transaksi', [AdminTransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('transaksi/{transaksi}', [AdminTransaksiController::class, 'show'])->name('transaksi.show');
        Route::get('transaksi/{transaksi}/cetak', [AdminTransaksiController::class, 'cetakStruk'])->name('transaksi.cetak');
    });

});

// Pelanggan Routes
use App\Http\Controllers\PelangganController;

Route::get('/pesan-takeaway', [PelangganController::class, 'takeaway'])->name('takeaway');

Route::prefix('menu-meja')->name('pelanggan.')->group(function () {
    Route::get('/{nomorMeja?}', [PelangganController::class, 'menu'])->name('menu');
    Route::get('/kategori/{kategori}', [PelangganController::class, 'getMenuByKategori'])->name('menu.kategori');
    Route::get('/search', [PelangganController::class, 'searchMenu'])->name('menu.search');

    Route::get('/menu-populer', [PelangganController::class, 'menuPopuler'])->name('menu.populer');

    Route::middleware('auth')->group(function () {
        Route::post('/cart/add', [PelangganController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/update', [PelangganController::class, 'updateCart'])->name('cart.update');
        Route::post('/cart/remove', [PelangganController::class, 'removeFromCart'])->name('cart.remove');
        Route::get('/cart', [PelangganController::class, 'getCart'])->name('cart');
        Route::post('/cart/clear', [PelangganController::class, 'clearCart'])->name('cart.clear');
        Route::post('/order', [PelangganController::class, 'submitOrder'])->name('order');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/riwayat', [PelangganController::class, 'riwayat'])->name('pelanggan.riwayat');
    Route::get('/booking', [PelangganController::class, 'bookingKaraoke'])->name('pelanggan.booking');
    Route::post('/booking', [PelangganController::class, 'storeBooking'])->name('pelanggan.booking.store');
    Route::get('/booking/cek', [PelangganController::class, 'cekKetersediaan'])->name('pelanggan.booking.cek');
    Route::get('/booking/daftar', [PelangganController::class, 'daftarBooking'])->name('pelanggan.booking.daftar');
});

// Rating
use App\Http\Controllers\Admin\RatingController;
Route::middleware('auth')->group(function () {
    Route::post('/rating', [RatingController::class, 'store'])->name('rating.store');
    Route::get('/menu/{menu}/ratings', [RatingController::class, 'byMenu'])->name('rating.by-menu');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Fallback Dashboard redirect for Auth systems
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isAdmin() || $user->isKasir() || $user->isKaryawan()) {
        if ($user->isKasir()) {
            return redirect()->route('admin.pos');
        }
        if ($user->isKaryawan()) {
            return redirect()->route('admin.karyawan.dashboard');
        }
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('pelanggan.menu');
})->middleware(['auth'])->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('auth/google', [\App\Http\Controllers\Auth\SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [\App\Http\Controllers\Auth\SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

require __DIR__.'/auth.php';
