@extends('admin.layouts.app')
@section('title', 'Dashboard Karyawan')

@push('styles')
<style>
    .stat-card {
        border-radius: 20px; padding: 1.5rem;
        position: relative; overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 60px rgba(0,0,0,0.12); }
    .stat-card::before {
        content: ''; position: absolute; top: -30px; right: -30px;
        width: 100px; height: 100px; border-radius: 50%;
        background: rgba(255,255,255,0.08);
    }
    .stat-card .card-icon {
        width: 48px; height: 48px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px); border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; color: #fff;
    }
    .stat-card .card-label { font-size: 0.78rem; color: rgba(255,255,255,0.75); font-weight: 500; }
    .stat-card .card-value { font-size: 1.75rem; font-weight: 800; color: #fff; line-height: 1.1; letter-spacing: -0.5px; }
    .stat-green  { background: linear-gradient(135deg, #059669, #10b981); }
    .stat-blue   { background: linear-gradient(135deg, #2563eb, #3b82f6); }
    .stat-purple { background: linear-gradient(135deg, #7c3aed, #a855f7); }
    .stat-orange { background: linear-gradient(135deg, #ea580c, #f97316); }
</style>
@endpush

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-gray-900">Halo, {{ explode(' ', Auth::user()->name)[0] }}!</h2>
    <p class="text-gray-500 text-sm mt-0.5">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card stat-green">
        <div class="flex items-start justify-between mb-3">
            <div class="card-icon"><i class="fa-solid fa-clock"></i></div>
        </div>
        <div class="card-label">Pesanan Aktif</div>
        <div class="card-value">{{ $todayOrders }}</div>
        <p class="text-xs text-white/70 mt-1">menunggu / diproses</p>
    </div>

    <div class="stat-card stat-blue">
        <div class="flex items-start justify-between mb-3">
            <div class="card-icon"><i class="fa-solid fa-microphone"></i></div>
        </div>
        <div class="card-label">Ruangan Aktif</div>
        <div class="card-value">{{ $activeRooms }}</div>
        <p class="text-xs text-white/70 mt-1">karaoke sedang berlangsung</p>
    </div>

    <div class="stat-card stat-purple">
        <div class="flex items-start justify-between mb-3">
            <div class="card-icon"><i class="fa-solid fa-calendar-check"></i></div>
        </div>
        <div class="card-label">Booking Hari Ini</div>
        <div class="card-value">{{ $todayBookings }}</div>
        <p class="text-xs text-white/70 mt-1">jadwal karaoke</p>
    </div>

    <div class="stat-card stat-orange">
        <div class="flex items-start justify-between mb-3">
            <div class="card-icon"><i class="fa-solid fa-utensils"></i></div>
        </div>
        <div class="card-label">Menu Tersedia</div>
        <div class="card-value">{{ $menuCount }}</div>
        <p class="text-xs text-white/70 mt-1">item siap saji</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="font-bold text-gray-800 mb-1">📍 Absensi Hari Ini</h3>
        <div class="mt-3">
            @if($myLatestAbsensi)
                <div class="flex items-center gap-3 p-3 rounded-xl {{ $myLatestAbsensi->jam_masuk && !$myLatestAbsensi->jam_keluar ? 'bg-green-50' : 'bg-gray-50' }}">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $myLatestAbsensi->jam_masuk && !$myLatestAbsensi->jam_keluar ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-500' }}">
                        <i class="fa-solid {{ $myLatestAbsensi->jam_masuk && !$myLatestAbsensi->jam_keluar ? 'fa-clock' : 'fa-check' }}"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-gray-800">
                            @if($myLatestAbsensi->jam_masuk && !$myLatestAbsensi->jam_keluar)
                                Sudah Clock In
                            @elseif($myLatestAbsensi->jam_masuk && $myLatestAbsensi->jam_keluar)
                                Sudah Clock Out
                            @else
                                Belum absen
                            @endif
                        </p>
                        <p class="text-xs text-gray-400">
                            @if($myLatestAbsensi->jam_masuk)
                                Masuk: {{ \Carbon\Carbon::parse($myLatestAbsensi->jam_masuk)->format('H:i') }}
                            @endif
                            @if($myLatestAbsensi->jam_keluar)
                                | Keluar: {{ \Carbon\Carbon::parse($myLatestAbsensi->jam_keluar)->format('H:i') }}
                            @endif
                        </p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-gray-800">Belum Absen</p>
                        <p class="text-xs text-gray-400">Silakan clock in melalui menu Absensi</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="font-bold text-gray-800 mb-1">🔗 Menu Cepat</h3>
        <div class="grid grid-cols-2 gap-3 mt-3">
            <a href="{{ route('admin.absensi.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-emerald-50 transition no-underline">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700">Absensi</span>
            </a>
            <a href="{{ route('admin.karyawan.menu') }}" class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-emerald-50 transition no-underline">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700">Menu</span>
            </a>
            <a href="{{ route('admin.karyawan.booking') }}" class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-emerald-50 transition no-underline">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700">Booking</span>
            </a>
            <a href="{{ route('admin.karyawan.pesanan') }}" class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-emerald-50 transition no-underline">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700">Pesanan</span>
            </a>
        </div>
    </div>
</div>
@endsection
