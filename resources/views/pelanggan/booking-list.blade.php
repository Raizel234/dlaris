@extends('layouts.pelanggan')
@section('title', 'Booking Saya')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Booking Saya</h2>
            <p class="text-muted mb-0">Daftar booking ruangan karaoke Anda</p>
        </div>
        <a href="{{ route('pelanggan.booking') }}" class="btn btn-success rounded-pill">
            <i class="bi bi-plus-lg me-1"></i>Booking Baru
        </a>
    </div>

    @if($bookings->count() > 0)
        <div class="row g-3">
            @foreach($bookings as $booking)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <span class="fw-bold h6 mb-0">{{ $booking->ruangan->nama }}</span>
                                <span class="ms-2 badge bg-light text-muted"><i class="bi bi-people me-1"></i>{{ $booking->ruangan->kapasitas }} org</span>
                            </div>
                            <span class="badge rounded-pill px-3 py-1
                                {{ $booking->status === 'pending' ? 'bg-warning-subtle text-warning' : '' }}
                                {{ $booking->status === 'confirmed' ? 'bg-primary-subtle text-primary' : '' }}
                                {{ $booking->status === 'ongoing' ? 'bg-success-subtle text-success' : '' }}
                                {{ $booking->status === 'selesai' ? 'bg-secondary-subtle text-secondary' : '' }}
                                {{ $booking->status === 'dibatalkan' ? 'bg-danger-subtle text-danger' : '' }}">
                                {{ $booking->status === 'confirmed' ? 'Confirmed' : ucfirst($booking->status) }}
                            </span>
                        </div>
                        <div class="row g-2 small mb-2">
                            <div class="col-6"><span class="text-muted"><i class="bi bi-calendar me-1"></i>Tanggal:</span> {{ $booking->tanggal->format('d/m/Y') }}</div>
                            <div class="col-6"><span class="text-muted"><i class="bi bi-clock me-1"></i>Jam:</span> {{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }}</div>
                            <div class="col-6"><span class="text-muted"><i class="bi bi-hourglass me-1"></i>Durasi:</span> {{ $booking->durasi }} jam</div>
                            <div class="col-6"><span class="text-muted"><i class="bi bi-cash me-1"></i>Total:</span> <span class="fw-bold text-success">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</span></div>
                        </div>
                        @if($booking->catatan)
                        <div class="small bg-light rounded p-2 text-muted"><i class="bi bi-chat me-1"></i>{{ $booking->catatan }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">
            {{ $bookings->links() }}
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar display-1 mb-3 d-block"></i>
            <h5>Belum ada booking</h5>
            <p class="mb-3">Pesan ruangan karaoke sekarang!</p>
            <a href="{{ route('pelanggan.booking') }}" class="btn btn-success rounded-pill">
                <i class="bi bi-plus-lg me-1"></i>Booking Sekarang
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<style>
.pagination { margin-bottom: 0; }
.page-link { border-radius: 50px !important; margin: 0 2px; color: #059669; }
.page-item.active .page-link { background-color: #059669; border-color: #059669; }
</style>
@endpush