@extends('admin.layouts.app')
@section('title', 'Booking Karaoke')

@push('styles')
<style>
    .booking-card {
        border-radius: 16px; border: 1px solid #f1f5f9;
        background: #fff; transition: all 0.3s;
    }
    .booking-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
    .status-pending { background: #fef9c3; color: #a16207; }
    .status-confirmed { background: #dbeafe; color: #1d4ed8; }
    .status-ongoing { background: #dcfce7; color: #15803d; }
    .status-selesai { background: #f1f5f9; color: #64748b; }
    .status-dibatalkan { background: #fef2f2; color: #dc2626; }
    .status-badge { font-size: 0.65rem; font-weight: 700; padding: 2px 10px; border-radius: 50px; text-transform: capitalize; }
    .room-status {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.72rem; font-weight: 600;
    }
    .room-tersedia { background: #dcfce7; color: #15803d; }
    .room-digunakan { background: #fef9c3; color: #a16207; }
    .room-maintenance { background: #f1f5f9; color: #64748b; }
</style>
@endpush

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">🎤 Booking Karaoke Hari Ini</h2>
    <p class="text-gray-500 text-sm mt-0.5">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
</div>

{{-- Status Ruangan --}}
<div class="mb-6">
    <h3 class="font-semibold text-gray-700 mb-3 text-sm">Status Ruangan</h3>
    <div class="flex flex-wrap gap-2">
        @foreach($ruangans as $ruangan)
            @php
                $statusClass = match($ruangan->status) {
                    'tersedia' => 'room-tersedia',
                    'digunakan' => 'room-digunakan',
                    default => 'room-maintenance',
                };
                $statusIcon = match($ruangan->status) {
                    'tersedia' => 'fa-check-circle',
                    'digunakan' => 'fa-microphone',
                    default => 'fa-tools',
                };
            @endphp
            <span class="room-status {{ $statusClass }}">
                <i class="fa-solid {{ $statusIcon }}"></i>
                {{ $ruangan->nama }} — {{ ucfirst($ruangan->status) }}
            </span>
        @endforeach
    </div>
</div>

{{-- Daftar Booking --}}
@forelse($bookings as $booking)
    <div class="booking-card p-4 mb-3">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <h4 class="font-bold text-gray-800">{{ $booking->nama_pemesan }}</h4>
                    <span class="status-badge status-{{ $booking->status }}">{{ $booking->status }}</span>
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500">
                    <span><i class="fa-solid fa-music w-4 text-gray-400"></i> {{ $booking->ruangan->nama }}</span>
                    <span><i class="fa-solid fa-clock w-4 text-gray-400"></i> {{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }}</span>
                    <span><i class="fa-solid fa-hourglass-half w-4 text-gray-400"></i> {{ $booking->durasi }} jam</span>
                    @if($booking->nomor_hp)
                        <span><i class="fa-solid fa-phone w-4 text-gray-400"></i> {{ $booking->nomor_hp }}</span>
                    @endif
                </div>
                @if($booking->catatan)
                    <p class="text-xs text-gray-400 mt-2 italic">{{ $booking->catatan }}</p>
                @endif
            </div>
            <div class="text-right flex-shrink-0 ml-4">
                <p class="font-bold text-emerald-600">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
@empty
    <div class="text-center py-16 text-gray-400">
        <i class="fa-solid fa-calendar-day text-5xl mb-4 block"></i>
        <p class="text-lg font-semibold">Tidak ada booking hari ini</p>
        <p class="text-sm">Belum ada jadwal karaoke untuk hari ini</p>
    </div>
@endforelse
@endsection
